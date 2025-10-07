<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;
    public $password;
    /**
     * Create a new message instance.
     *
     * @param \App\Model\Employee|\App\Models\Employee $employee
     * @param string $password the default password (plain)
     */
    public function __construct($employee, string $password)
    {
        $this->employee = $employee;
        $this->password = $password;
    }

    public function build()
    {
        // subject can be adjusted
        return $this->subject('Welcome — access details for ' . config('app.name'))
            ->view('emails.employee_welcome');
    }
}
