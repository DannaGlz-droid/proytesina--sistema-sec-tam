<?php

namespace App\Mail;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ReportRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $rejectorFullName;

    public function __construct(
        public Publication $publication,
        public User $rejector,
        public string $reason,
        public string $reportUrl
    ) {
        $this->rejectorFullName = trim(implode(' ', array_filter([
            $rejector->name,
            $rejector->first_last_name,
            $rejector->second_last_name,
        ])));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reporte rechazado: “' . Str::limit($this->publication->topic ?? 'Sin título', 90) . '”'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reportes.rejected'
        );
    }
}
