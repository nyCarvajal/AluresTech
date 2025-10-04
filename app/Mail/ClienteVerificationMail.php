<?php

namespace App\Mail;

use App\Models\Cliente;
use App\Models\Peluqueria;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClienteVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peluqueria $peluqueria,
        public Cliente $cliente,
        public string $verificationUrl
    ) {
    }

    public function build(): self
    {
        return $this->subject('Confirma tu correo en ' . $this->peluqueria->nombre)
            ->markdown('emails.clientes.verify')
            ->with([
                'peluqueria' => $this->peluqueria,
                'cliente' => $this->cliente,
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}
