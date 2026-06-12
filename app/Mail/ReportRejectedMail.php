<?php

namespace App\Mail;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Publication $publication,
        public User $rejector,
        public string $reason,
        public string $reportUrl
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte rechazado: ' . ($this->publication->topic ?? 'Sin titulo')
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reportes.rejected'
        );
    }
}
