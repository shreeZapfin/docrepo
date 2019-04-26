<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Pickuppdf extends Mailable
{
    use Queueable, SerializesModels;
    public  $file;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($file,$data)
    {
         $this->file = $file;
         $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail =  $this->from(env('MAIL_USERNAME', 'info@docboyz.in'))
            ->subject('DocBoys | '. ucfirst($this->data->subject_name ));
             if (isset($this->data->cc_1)){
                 $mail = $mail->cc($this->data->cc_1);
             }
        //$mail->bcc(['poojakakde65@gmail.com','patilrahul9923@gmail.com','spsandeep1@gmail.com','mesanketshah@gmail.com']);
        $mail->markdown('emails.pickup_mail');
        $mail->attach($this->file);
        return $mail;
    }
}


