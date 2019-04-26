<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PickupMail extends Mailable
{
    use Queueable, SerializesModels;
    public $pickup;
    protected $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pickup)
    {
        $this->pickup = $pickup;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('view.name');
        return $this->from('support@docboyz.in')
            ->subject('DocBoyz | '.trans('mails.forgot_password_for', []).' '.ucfirst($this->pickup->name).'!')
            ->markdown('emails.forgot-password')
            ->view('admin.pickups.pdf')
            ->attachData($this->pdf, 'name.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
