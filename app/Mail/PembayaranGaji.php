<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class PembayaranGaji extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $pdfContent;

    public function __construct($data, $pdfContent)
    {
        $this->data = $data;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->subject('Slip Pembayaran Gaji')
                    ->view('emails.pembayaran-gaji')
                    ->with('data', $this->data)
                    ->attachData($this->pdfContent, 'slip-gaji.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
