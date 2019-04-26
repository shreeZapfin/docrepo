<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerPickupMail extends Mailable
{
    use Queueable, SerializesModels;
    public  $data;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {

        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from(env('MAIL_USERNAME', 'info@docboyz.in'));
        $mail->subject('DocBoys | '. ucfirst($this->data->subject_name ));
        if (isset($this->data->to_cc)){
            $mail = $mail->cc($this->data->to_cc);
        }
        if (isset($this->data->to_bcc)){
            $mail = $mail->bcc($this->data->to_bcc);
        }
        $mail->markdown('emails.customer_pickup_email');
        $mail->attach($this->data->file->store("xlsx",false,true)['full']);
        return $mail;

    }
}
