<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

class WhatsappWebhookController extends Controller
{
    public function __invoke(Request $r)
    {
        // 1) Validación inicial (Meta envía GET)
        if ($r->isMethod('get')) {
            return $r->hub_mode === 'subscribe' &&
                   $r->hub_verify_token === env('WHATSAPP_VERIFY_TOKEN')
                ? response($r->hub_challenge, 200)
                : abort(403, 'Token no válido');
        }

        // 2) Mensajes / estados
        \Log::channel('whatsapp')->info($r->getContent());
        return response()->noContent();
    }
}

?>