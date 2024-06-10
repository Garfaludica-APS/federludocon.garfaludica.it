<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingInvitation extends Mailable
{
	use SerializesModels;

	/**
	 * Create a new message instance.
	 */
	public function __construct(public string $name, public string $date) {}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			subject: '[Garfaludica APS] Mancano pochi giorni alla GobCon! Prenota il tuo posto!',
		);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			markdown: 'mail.markdown.booking-invitation',
			with: [
				'name' => $this->name,
				'date' => $this->date,
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
		return [];
	}
}
