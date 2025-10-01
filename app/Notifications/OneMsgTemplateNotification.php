<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\OneMsgChannel;

class OneMsgTemplateNotification extends Notification
{
    use Queueable;

    /**
     * @param  string  $templateKey   clave: reserva, bienvenida, …
     * @param  array   $data          ej. ['tipo'=>'Reserva', 'duracion'=>90, ...]
     */
    public function __construct(private string $templateKey, private array $data) {}

    public function via($notifiable): array
    {
        return [OneMsgChannel::class];
    }

public function toOneMsgTemplate($notifiable): array
{
   
    $template  = $this->templateKey;
    $namespace = config('services.onemsg.namespace');
    $langCode  = config('services.onemsg.lang', 'es');

    // Orden esperado: header + 6 vars del body
    $order = config("services.onemsg.param_order.{$this->templateKey}");
	
if($template=="reserva"){
    // ---- 1. header ----
    $headerItem = [
        'type' => 'text',
        'text' => $this->data['header'] ?? 'Reserva',
    ];
	
}else if($template=="bienvenida"){
	// ---- 1. header ----
    $headerItem = [
        'type' => 'text',
        'text' => $this->data[1],
    ];

}else if($template=="cambio_clase"){
	// ---- 1. header ----
    $headerItem = [
        'type' => 'text',
        'text' => $this->data[1],
    ];
}else if($template=="cancelacion"){
	// ---- 1. header ----
    $headerItem = [
        'type' => 'text',
        'text' => 'Tu '.$this->data[1].' ha sido cancelada',
    ];
}

    // ---- 2. body ----
     $bodyParams = collect($order)
        ->skip(0)
        ->map(fn($key) => [
            'type' => 'text',
            'text' => $this->data[$key] ?? '',
        ])
       // ->filter(fn($item) => trim($item['text']) !== '')
        ->values()
        ->toArray();
		
		if($template=="finalizado"){
	
	return [
        'namespace' => "36f05a4b_f53a_4676_a95e_239e854de795",
        'template'  => $template,
        'language'  => [
            'policy' => 'deterministic',
            'code'   => 'es',
        ],
        'params'    => [
           
            // Body params array
            [
                'type'       => 'body',
                'parameters' => $bodyParams,
            ],
        ],
        // phone se añade en el canal antes de enviar
    ];
	
}else{

    return [
        'namespace' => "36f05a4b_f53a_4676_a95e_239e854de795",
        'template'  => $template,
        'language'  => [
            'policy' => 'deterministic',
            'code'   => 'es',
        ],
        'params'    => [
            // Header params array
            [
                'type'       => 'header',
                'parameters' => [ $headerItem ],
            ],
            // Body params array
            [
                'type'       => 'body',
                'parameters' => $bodyParams,
            ],
        ],
        // phone se añade en el canal antes de enviar
    ];
}
}




}