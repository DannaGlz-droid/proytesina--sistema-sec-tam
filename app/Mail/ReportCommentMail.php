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

class ReportCommentMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Publication $publication,
        public PublicationComment $comment,
        public User $sender,
        public string $notificationTitle,
        public string $notificationMessage,
        public string $reportUrl
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->notificationTitle
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reportes.comment'
        );
    }
}
