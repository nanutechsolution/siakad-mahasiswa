<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PmbStatusUpdate extends Mailable
{
    use Queueable, SerializesModels;

    public $registrant;
    public $status; // ACCEPTED / REJECTED

    public function __construct(Registrant $registrant, $status)
    {
        $this->registrant = $registrant;
        $this->status = $status;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pmb Status Update',
        );
    }
    public function build()
    {
        $subject = $this->status == 'ACCEPTED'
            ? 'SELAMAT! Anda Lulus Seleksi PMB UNMARIS'
            : 'Pemberitahuan Hasil Seleksi PMB UNMARIS';

        return $this->subject($subject)
            ->view('emails.pmb-status');
    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
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
