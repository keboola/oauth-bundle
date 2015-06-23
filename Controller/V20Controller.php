<?php

namespace Keboola\OAuthBundle\Controller;

use	Keboola\Syrup\Exception\UserException;
use	Symfony\Component\HttpFoundation\Response,
	Symfony\Component\HttpFoundation\Request;
use	GuzzleHttp\Client as GuzzleClient,
	GuzzleHttp\Exception\ClientException;
use	Keboola\Utils\Utils;
use	Keboola\OAuth\OAuth20Controller;

/**
 * {@inheritdoc}
 */
class V20Controller extends OAuth20Controller
{
	/**
	 * @var array
	 */
	protected $api;

	protected $requiredParams = ['api', 'token', 'id'];

	protected function getConsumer($api)
	{
		/**
		* @var \Doctrine\DBAL\Connection
		*/
		$conn = $this->getDoctrine()->getConnection('oauth_providers');

		$consumer = $conn->fetchAssoc("SELECT * FROM `consumers_v2` WHERE `id` = '{$api}'");
		if (empty($consumer)) {
			throw new UserException("Api '{$api}' details not found in the OAuth 2.0 consumer database");
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

	protected function getOAuthUrl($redirUrl, $clientId, $hash)
	{
		$url = $this->api['oauth_url'];
		$url = str_replace('%%redirect_uri%%', $redirUrl, $url);
		$url = str_replace('%%client_id%%', $clientId, $url);
		$url = str_replace('%%hash%%', $hash, $url);
		return $url;
	}

	protected function getTokenUrl()
	{
		return $this->api['token_url'];
	}

	protected function getAppParams()
	{
		// return [client-id & client-secret]
		return [
			'client-id' => $this->api['client_id'],
			'client-secret' => $this->api['client_secret']
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

		$conn->insert('credentials', [
			'id' => $this->sessionBag->get('id'),
			'api' => $this->api['id'],
			'consumer_key' => $this->api['client_id'],
			'oauth_version' => '2.0',
			'project' => $token['owner']['id'],
			'creator' => $token['token'],
			'data' => json_encode($data)
		]);
	}
}
