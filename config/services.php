<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services.
	|
	*/

	'mediawiki' => [
		'identifier' => env( 'MEDIAWIKI_IDENTIFIER' ),
		'secret' => env( 'MEDIAWIKI_SECRET' ),
		'callback_uri' => env( 'MEDIAWIKI_CALLBACK' ),
		'client_id' => env('MEDIAWIKI_IDENTIFIER'),
		'client_secret' => env('MEDIAWIKI_SECRET'),
		'redirect' => env('MEDIAWIKI_CALLBACK'),
		'base_url' => env( 'MEDIAWIKI_BASE' ),
	],
];
