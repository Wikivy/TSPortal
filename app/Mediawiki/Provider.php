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
		return (new User)->setRaw($user)->map([
			'id'       => $user['sub'],
			'username' => $user['username'],
			'editcount' => $user['editcount'],
			'email_verified' => $user['email_verified'],
			'blocked' => $user['blocked'],
			'groups' => $user['groups'],
			'rights' => $user['rights'],
			'grants' => $user['grants'],
			'email'    => Arr::get($user, 'email'),
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
