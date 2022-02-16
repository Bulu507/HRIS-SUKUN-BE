<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailApp extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('admin@aqwam.com')
                   ->view('emailku')
                   ->with(
                    [
                        'nama' => 'Cemara IT Solution',
                        'no_invoice' => 'ORD-21I01-053456YG',
                        'expedisi' => 'JNE (OKE)',
                        'resi' => 'SCO01202000111',
                    ]);
    }
}
