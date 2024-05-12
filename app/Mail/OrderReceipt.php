<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReceipt extends Mailable
{
	use Queueable;
	use SerializesModels;

	/**
	 * Create a new message instance.
	 */
	public function __construct(public Booking $booking, public string $receiptPath) {}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			subject: __('[Garfaludica APS] GobCon 2024 - Order confirmed!'),
		);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			markdown: 'mail.markdown.receipt',
			with: [
				'booking' => $this->booking,
				'logoPath' => storage_path('images/logo.png'),
			],
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
	 */
	public function attachments(): array
	{
		return [
			Attachment::fromPath($this->receiptPath)
				->as('receipt.pdf')
				->withMime('application/pdf'),
		];
	}
}
