<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class UsernameRule implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Conduct a HTTP request.
	 *
	 * @param string $attribute
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public function passes( $attribute, $value ): bool
	{
		return ( Http::get( config( 'app.mediawiki_api_base' ) . '/api.php?format=json&action=query&meta=globaluserinfo&guiuser=' . htmlspecialchars( $value ) )['query']['globaluserinfo']['id'] ?? false );
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message(): string
	{
		return __( 'username-exist' );
	}
}
