<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class OneMsgChannel
{
   
   public function send($notifiable, Notification $notification): void
{
	
	
    /* 1) Payload que ya construyes en toOneMsgTemplate() */
    $payload = $notification->toOneMsgTemplate($notifiable);
    if (!$payload) { return; }

    /* 2) Completar phone (sin +) */
    $phone= ltrim(
        $notifiable->routeNotificationFor('onemsg') ?? $notifiable->telefono,
        '+'
    );
	 $payload['phone']  = '57' . ltrim($phone, '0');
	 
	 
	
	$url = sprintf(
    'https://api.1msg.io/%s/sendTemplate?token=%s',
    'VID163266002',
    'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpbnN0YW5jZUlkIjoiVklEMTYzMjY2MDAyIiwidG9rZW4iOiJUS05IR0NUQ0dxQXU5ZkJEWG5ZR2JyWFRMa2ZZMk04eSIsImlzcyI6IjFtc2cuaW8iLCJpYXQiOjE3NTc2MTAyNjh9.z8PuvZU58Dc324ihGAQC64a4Uh_3B2lpH4aYC5-3CmM'
);

$jsonBody = json_encode($payload, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])
        ->withBody($jsonBody, 'application/json')
        ->post($url)
        ->throw();

/*
dd('[1MSG] PAYLOAD', [
    'url'     => $url,        // endpoint completo
    'payload' => $jsonBody,    // JSON que se enviará
]);

*/
//$response = Http::asJson()->post($url, $payload)->throw();
logger()->debug('1MSG RAW', $response->json());

$json = $response->json();
if (!$json || !($json['sent'] ?? false)) {
    throw new \RuntimeException(
        '1MSG error: '.($json['message'] ?? $json['error'] ?? 'unknown')
    );
}
	
   
}
}
