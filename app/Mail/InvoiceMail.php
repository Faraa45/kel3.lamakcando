<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// tambahan
use Illuminate\Mail\Mailables\Attachment;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    // tambahan untuk data dan konten pdf
    public $data;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    // public function __construct()
    // {
    //     //
    // }

    public function __construct($data, $pdfContent)
    {
        $this->data = $data;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Pembelian lamak cando',
        );
    }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'emails.invoice',
    //         with: [
    //             'data' => $this->data,
    //         ],
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            // tambahkan pemrosesan attachment
            Attachment::fromData(fn () => $this->pdfContent, 'invoice.pdf')
            ->withMime('application/pdf'),
        ];
    }

    public function build()
{
return $this->subject('Invoice Pembelian')
            ->view('emails.invoice')
            ->attachData($this->pdfContent, 'invoice.pdf', [
                'mime' => 'application/pdf',
            ])
            ->with([
                'nama_vendor' => $this->data['nama_vendor'],
                'no_faktur' => $this->data['no_faktur'],
            ]);
}

    
}