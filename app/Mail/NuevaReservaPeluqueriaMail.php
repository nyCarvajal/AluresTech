<?php

namespace App\Mail;

use App\Models\Cliente;
use App\Models\Peluqueria;
use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaReservaPeluqueriaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Peluqueria $peluqueria,
        public Cliente $cliente,
        public Reserva $reserva
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Nueva solicitud de reserva')
            ->markdown('emails.peluquerias.nueva-reserva');
    }
}
