<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order]);

        return $this->subject('Hóa đơn đơn hàng #' . $this->order->code)
                    ->markdown('emails.invoice')
                    ->attachData($pdf->output(), 'invoice_' . $this->order->code . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
