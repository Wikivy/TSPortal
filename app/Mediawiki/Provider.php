<?php

namespace App\Mediawiki;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
	public const IDENTIFIER = 'MEDIAWIKI';

	protected $scopes = [];

	protected $scopeSeparator = ' ';

	public static function additionalConfigKeys(): array
	{
		return [
			'base_url',
		];
	}

	protected function getAuthUrl($state): string
	{
		return $this->buildAuthUrlFromBase($this->getMediawikiUrl('authorize_uri'), $state);
	}

	protected function getTokenUrl(): string
	{
		return $this->getMediawikiUrl('token_uri');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getUserByToken($token)
	{
		$response = $this->getHttpClient()->get(
			$this->getMediawikiUrl('userinfo_uri'),
			[
				RequestOptions::HEADERS => [
					'Accept'        => 'application/json',
					'Authorization' => 'Bearer '.$token
				],
			]
		);

		return json_decode((string) $response->getBody(), true);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function mapUserToObject(array $user)
	{
		$user = $user['data']['0'];

		return (new User)->setRaw($user)->map([
			'id'       => $user['id'],
			'nickname' => $user['display_name'],
			'name'     => $user['display_name'],
			'email'    => Arr::get($user, 'email'),
			'avatar'   => $user['profile_image_url'],
		]);
	}

	protected function getMediawikiUrl($type)
	{
		return rtrim($this->getConfig('base_url'), '/').'/'.ltrim($this->getConfig($type, Arr::get([
				'authorize_uri' => 'rest.php/oauth2/authorize',
				'token_uri'     => 'rest.php/oauth2/access_token',
				'userinfo_uri'  => 'rest.php/oauth2/resource/profile',
			], $type)), '/');
	}
}
