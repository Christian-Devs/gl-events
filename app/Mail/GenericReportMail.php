<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $pdfContent;

    public function __construct($title, $pdfContent)
    {
        $this->title = $title;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->subject("{$this->title} Report")
            ->view('emails.generic_report')
            ->attachData($this->pdfContent, "{$this->title}_Report.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}

