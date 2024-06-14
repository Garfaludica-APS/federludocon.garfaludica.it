<?php

namespace App\Console\Commands;

use App\Enums\Menu;
use App\Models\Booking;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendReceipt extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'app:send-receipt
					{id : Booking ID}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send booking receipt';

	/**
	 * Execute the console command.
	 */
	public function handle(): void
	{
		$id = $this->argument('id');

		$booking = Booking::findOrFail($id);

		$orderId = 'GARFALUDICA-MANUAL-SET-ORDER';

		$imageContent = Storage::get('garfaludica.jpg');
		$logoUrl = 'data:image/jpeg;base64,' . base64_encode($imageContent);

		$orderIdLabel = __('Order ID');
		$receiptNumLabel = __('Receipt #');
		$dateLabel = __('Date');
		$codeLabel = __('Code');
		$itemHeadLabel = __('Room/Meal');
		$priceHeadLabel = __('Price');
		$discountLabel = __('Discount');
		$totalLabel = __('Total');
		$receiptNote = __('Pro-forma, non-fiscal receipt');

		$receiptNum = 'GARFALUDICA-' . mb_str_pad((string)($booking->short_id), 4, '0', \STR_PAD_LEFT);
		$date = now()->setTimezone('Europe/Rome')->format('Y-m-d H:i:s');
		$code = $booking->id;

		$firstName = htmlspecialchars($booking->billingInfo->first_name);
		$lastName = htmlspecialchars($booking->billingInfo->last_name);
		$taxId = htmlspecialchars($booking->billingInfo->tax_id);
		$address = htmlspecialchars($booking->billingInfo->address_line_1) . ($booking->billingInfo->address_line_2 ? '<br />' . htmlspecialchars($booking->billingInfo->address_line_2) : '');
		$city = htmlspecialchars($booking->billingInfo->city);
		$state = htmlspecialchars($booking->billingInfo->state);
		$postalCode = htmlspecialchars($booking->billingInfo->postal_code);
		$countryCode = htmlspecialchars(strtoupper($booking->billingInfo->country_code));
		$email = htmlspecialchars($booking->billingInfo->email);
		$phone = htmlspecialchars($booking->billingInfo->phone ?? '');

		$locale = app()->isLocale('it') ? 'it' : 'en';
		$total = 0.0;
		$discount = 0.0;
		$items = [];
		$rooms = $booking->rooms()->with('room', 'room.hotel')->get();

		foreach ($rooms as $room) {
			$desc = $room->room->name[$locale];
			if ($room->buy_option[$locale] !== 'default')
				$desc .= ' (' . $room->buy_option[$locale] . ')';
			$desc .= ' - ' . __('hotel_name_' . $room->room->hotel->name);
			$desc .= ' [' . $room->checkin->format('d/m') . ' - ' . $room->checkout->format('d/m') . ']';
			$items[] = [
				'label' => htmlspecialchars($desc),
				'price' => '€ ' . number_format((float)($room->price), 2, '.', ''),
			];
			$total += $room->price;
		}
		$meals = $booking->meals()->with('meal', 'meal.hotel')->get();

		foreach ($meals as $meal) {
			$desc = $meal->quantity . 'x ';
			$desc .= __($meal->meal->type->value);
			if ($meal->meal->menu !== Menu::STANDARD)
				$desc .= ' (' . __($meal->meal->menu->value) . ')';
			$desc .= ' - ' . __('hotel_name_' . $meal->meal->hotel->name);
			$desc .= ' [' . $meal->date->format('d/m') . ']';
			$items[] = [
				'label' => htmlspecialchars($desc),
				'price' => '€ ' . number_format((float)($meal->price), 2, '.', ''),
			];
			$total += $meal->price;
			$discount += $meal->discount;
		}

		$discount += floatval($booking->discount);
		$total -= $discount;
		$discount = '€ ' . number_format(-$discount, 2, '.', '');
		$total = '€ ' . number_format($total, 2, '.', '');

		$options = new Options();
		// $options->set('isRemoteEnabled', true);
		$dompdf = new Dompdf($options);

		$htmlTemplate = <<<EOD

					<html>
						<head>
							<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
							<style>
								.invoice-box {
									max-width: 800px;
									margin: auto;
									padding: 30px;
									border: 1px solid #eee;
									box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
									font-size: 14px;
									line-height: 20px;
									font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
									color: #555;
								}

								.invoice-box table {
									width: 100%;
									line-height: inherit;
									text-align: left;
								}

								.invoice-box table td {
									padding: 5px;
									vertical-align: top;
								}

								.invoice-box table tr td:nth-child(2) {
									text-align: right;
								}

								.invoice-box table tr.top table td {
									padding-bottom: 20px;
								}

								.invoice-box table tr.top table td.title {
									font-size: 20px;
									line-height: 30px;
									color: #333;
								}

								.invoice-box table tr.information table td {
									padding-bottom: 40px;
								}

								.invoice-box table tr.heading td {
									background: #eee;
									border-bottom: 1px solid #ddd;
									font-weight: bold;
								}

								.invoice-box table tr.details td {
									padding-bottom: 20px;
								}

								.invoice-box table tr.item td {
									border-bottom: 1px solid #eee;
								}

								.invoice-box table tr.item.last td {
									border-bottom: none;
								}

								.invoice-box table tr.total td:nth-child(2) {
									border-top: 2px solid #eee;
									font-weight: bold;
								}

								@media only screen and (max-width: 600px) {
									.invoice-box table tr.top table td {
										width: 100%;
										display: block;
										text-align: center;
									}

									.invoice-box table tr.information table td {
										width: 100%;
										display: block;
										text-align: center;
									}
								}

								/** RTL **/
								.invoice-box.rtl {
									direction: rtl;
									font-family: Tahoma, "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
								}

								.invoice-box.rtl table {
									text-align: right;
								}

								.invoice-box.rtl table tr td:nth-child(2) {
									text-align: left;
								}
							</style>
						</head>

						<body>
							<div class="invoice-box">
								<table cellpadding="0" cellspacing="0">
									<tr class="top">
										<td colspan="2">
											<table>
												<tr>
													<td class="title">
														<img src="$logoUrl" style="width: 100%; max-width: 150px" />
													</td>
													<td>
														{$receiptNumLabel}: {$receiptNum}<br />
														{$dateLabel}: {$date}<br />
														{$orderIdLabel}: {$orderId}<br />
														{$codeLabel}: {$code}<br />
														{$receiptNote}
													</td>
												</tr>
											</table>
										</td>
									</tr>

									<tr class="information">
										<td colspan="2">
											<table>
												<tr>
													<td>
														Garfaludica APS<br />
														Ente del Terzo Settore (RUNTS 113019)<br />
														Tana dei Goblin di Castelnuovo di Garfagnana<br />
														C.F.: 90011570463<br />
														Località Braccicorti, 38/A - 55036 Pieve Fosciana (LU)<br />
														info@garfaludica.it - garfaludica@pec.it<br />
														https://www.garfaludica.it
													</td>
													<td>
														{$firstName} {$lastName}<br />
														{$taxId}<br />
														{$address}<br />
														{$city}, {$state} {$postalCode}<br />
														{$countryCode}<br />
														{$email}<br />
														{$phone}
													</td>
												</tr>
											</table>
										</td>
									</tr>

									<tr class="heading">
										<td>{$itemHeadLabel}</td>
										<td>{$priceHeadLabel}</td>
									</tr>

			EOD;

		foreach ($items as $item)
			$htmlTemplate .= <<<EOD

										<tr class="item">
											<td>{$item['label']}</td>
											<td>{$item['price']}</td>
										</tr>

				EOD;

		$htmlTemplate .= <<<EOD

									<tr class="item last">
										<td>{$discountLabel}</td>
										<td>{$discount}</td>
									</tr>

									<tr class="total">
										<td></td>
										<td>{$totalLabel}: {$total}</td>
									</tr>
								</table>
							</div>
						</body>
					</html>

			EOD;

		$dompdf->loadHtml($htmlTemplate);
		$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();

		Storage::put('receipts/' . $booking->id . '.pdf', $dompdf->output());
		$receiptPath = storage_path('app/receipts/' . $booking->id . '.pdf');

		//Mail::to($booking->email)->queue(new OrderReceipt($booking, $receiptPath));
	}
}
