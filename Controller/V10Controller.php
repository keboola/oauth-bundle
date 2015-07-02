<?php

namespace Keboola\OAuthBundle\Controller;

use	Keboola\Syrup\Exception\UserException;
use	Symfony\Component\HttpFoundation\Response,
	Symfony\Component\HttpFoundation\Request;
use	GuzzleHttp\Client as GuzzleClient,
	GuzzleHttp\Exception\ClientException;
use	Keboola\Utils\Utils;
use	Keboola\OAuth\OAuth10Controller;

/**
 * {@inheritdoc}
 */
class V10Controller extends OAuth10Controller
{
	/**
	 * @var array
	 */
	protected $api;

	protected $requiredParams = ['api', 'token', 'id'];

	protected function getAuthenticateUrl($oauthToken)
	{
		$url = $this->api['authenticate_url'];
		$url = str_replace('%%oauth_token%%', $oauthToken, $url);
		return $url;
	}

	protected function getRequestTokenUrl()
	{
		return $this->api['request_token_url'];
	}

	protected function getAccessTokenUrl()
	{
		return $this->api['access_token_url'];
	}

	protected function getConsumer($api)
	{
		/**
		* @var \Doctrine\DBAL\Connection
		*/
		$conn = $this->getDoctrine()->getConnection('oauth_providers');

		$consumer = $conn->fetchAssoc("SELECT * FROM `consumers_v1` WHERE `id` = :api", ['api' => $api]);
		if (empty($consumer)) {
			throw new UserException("Api '{$api}' details not found in the OAuth 1.0 consumer database");
		}
		return $consumer;
	}

	public function getOAuthAction(Request $request)
	{
		$api = $request->request->get('api');
		$this->api = $this->getConsumer($api);

		return parent::getOAuthAction($request);
	}

	public function getOAuthCallbackAction(Request $request)
	{
		$this->initSessionBag();
		$api = $this->sessionBag->get('api');
		$this->api = $this->getConsumer($api);

		return parent::getOAuthCallbackAction($request);
	}

	protected function getAppParams()
	{
		return [
			'api-key' => $this->api['consumer_key'],
			'api-secret' => $this->api['consumer_secret']
		];
	}

	protected function storeOAuthData($data)
	{
		$sapi = $this->getStorageApi();
		try {
			$token = $sapi->verifyToken();
		} catch(Keboola\StorageApi\ClientException $e) {
			throw new UserException($e->getMessage());
		}

		/**
		 * @var \Doctrine\DBAL\Connection
		 */
		$conn = $this->getDoctrine()->getConnection('oauth_providers');

		try {
			$conn->insert('credentials', [
				'id' => $this->sessionBag->get('id'),
				'api' => $this->api['id'],
				'consumer_key' => $this->api['consumer_key'],
				'oauth_version' => '1.0',
				'project' => $token['owner']['id'],
				'creator' => json_encode(['id' => $token['id'], 'description' => $token['description']]),
				'data' => json_encode($data),
				'description' => $this->sessionBag->has('description') ? $this->sessionBag->get('description') : ""
			]);
		} catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
			$id = $this->sessionBag->get('id');
			throw new UserException("Credentials '{$id}' for api '{$this->api['id']}' already exist!");
		}
	}
}
