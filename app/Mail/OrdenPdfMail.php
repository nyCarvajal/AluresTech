<?php
// app/Mail/OrdenPdfMail.php
namespace App\Mail;

use App\Models\OrdenDeCompra;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrdenPdfMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;
    public $mensaje;
    protected $pdfBinary;

    public function __construct(OrdenDeCompra $orden, string $pdfBinary, ?string $mensaje = null)
    {
        $this->orden = $orden;
        $this->pdfBinary = $pdfBinary;
        $this->mensaje = $mensaje;
    }

    public function build()
    {
        return $this->subject("Orden de Compra #{$this->orden->id}")
            ->view('emails.orden') // crea esta vista simple
            ->attachData($this->pdfBinary, "Orden-{$this->orden->id}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }
}
