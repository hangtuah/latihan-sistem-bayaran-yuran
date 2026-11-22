<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CollectionReportReady extends Mailable
{
    use Queueable, SerializesModels;

    /** @param string $path laluan dalam disk lalai, contohnya reports/kutipan-2025.csv */
    public function __construct(public string $path) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Laporan kutipan sedia');
    }

    public function content(): Content
    {
        return new Content(text: 'mail.collection-report-ready');
    }

    public function attachments(): array
    {
        return [Attachment::fromStorage($this->path)];
    }
}
