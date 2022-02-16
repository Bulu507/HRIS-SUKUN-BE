<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderReport extends Mailable
{
    use Queueable, SerializesModels;

    protected $Order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Order)
    {
        $this->Order = $Order;
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
                        'nama' => $this->Order->nama,
                        'no_invoice' => $this->Order->id,
                        'expedisi' => $this->Order->expedisi,
                        'resi' => $this->Order->no_resi,
                    ]);
    }
}
