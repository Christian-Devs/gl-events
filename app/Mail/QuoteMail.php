<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;
    public $pdfContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($quote, $pdfContent)
    {
        $this->quote = $quote;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Quote from Laravel')
            ->view('emails.quote')
            ->attachData($this->pdfContent, 'invoice-' . $this->quote->quote_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
