<?php

namespace App\Mail;

use App\Models\Publication;
use App\Models\PublicationComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ReportCommentMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $senderFullName;

    public function __construct(
        public Publication $publication,
        public PublicationComment $comment,
        public User $sender,
        public string $notificationTitle,
        public string $reportUrl
    ) {
        $this->senderFullName = trim(implode(' ', array_filter([
            $sender->name,
            $sender->first_last_name,
            $sender->second_last_name,
        ])));
    }

    public function envelope(): Envelope
    {
        $prefix = str_contains($this->notificationTitle, 'respondi')
            ? 'Nueva respuesta en'
            : 'Nuevo comentario en';

        return new Envelope(
            subject: $prefix . ' “' . Str::limit($this->publication->topic ?? 'Publicación', 90) . '”'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reportes.comment'
        );
    }
}
