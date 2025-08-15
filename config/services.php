<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
	
	

'onemsg' => [
    'channel_id' => env('ONEMSG_CHANNEL_ID'),
    'token'      => env('ONEMSG_TOKEN'),
	'namespace' => env('ONEMSG_TPL_NAMESPACE'),
    'lang'      => env('ONEMSG_TPL_LANG', 'es'),
    'templates'  => [
        'bienvenida'     => env('ONEMSG_TPL_BIENVENIDA'),
		'reserva'        => env('ONEMSG_TPL_RESERVA'),
        'cambio_clase'   => env('ONEMSG_TPL_CAMBIO_CLASE'),
        'cancelacion'    => env('ONEMSG_TPL_CANCELACION'),
        'paquete'        => env('ONEMSG_TPL_PAQUETE'),
		'finalizado'     => env('ONEMSG_TPL_FINALIZADO'),
    ],
    'lang' => [
        'default'        => env('ONEMSG_TPL_LANG', 'es'),
        'cancelacion'    => env('ONEMSG_TPL_LANG_CANC', env('ONEMSG_TPL_LANG', 'es')),
    ],
	/* ← orden que exige cada plantilla → */
    'param_order' => [
		
    // header primero, luego los 6 del body
    'reserva' => ['0','1', '2', '3', '4', '5'],

        // {{1}}        {{2}}             {{3}}        {{4}}
      //  'reserva'        => ['1', '2', '3', '4', '5', '6'],
        'bienvenida'     => ['0', '1'],
		'cambio_clase'   => ['0', '1', '2','3','4','5'],
		'cancelacion'    => ['0', '1', '2'],
		'paquete'        => ['0', '1', '2','3'],
		'finalizado'      => ['0', '1'],
        // …
    ],
],


];
