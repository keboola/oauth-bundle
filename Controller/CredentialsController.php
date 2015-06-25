<?php

namespace Keboola\OAuthBundle\Controller;

use	Keboola\Syrup\Controller\ApiController;
use	Symfony\Component\HttpFoundation\Response,
	Symfony\Component\HttpFoundation\Request;
use	Keboola\Utils\Utils;

class CredentialsController extends ApiController
{
    public function getAction($api, $id, Request $request)
    {
		$client = $this->storageApi;

		$token = $this->storageApi->verifyToken();

		/**
		 * @var \Doctrine\DBAL\Connection
		 */
		$conn = $this->getDoctrine()->getConnection('oauth_providers');

		$creds = $conn->fetchAssoc("SELECT `data`, `consumer_key`, `oauth_version` FROM `credentials` WHERE `project` = '{$token['owner']['id']}' AND `id` = '{$id}' AND `api` = '{$api}'");

		// TODO check for data

		$content = Utils::json_decode($request->getContent());

		// TODO makes consumer_secret available to anyone with a token, is that quite desirable?
		// It's needed for OAuth 1.0 clients :-/
// 		if (!empty($content->includeApiDetail)) {
// 			$consumerTable = $creds['oauth_version'] == '2.0' ? 'consumers_v2' : 'consumers_v1';
// 			$consumerColumn = $creds['oauth_version'] == '2.0' ? 'client_id' : 'consumer_key';
// 			$apiDetail = $conn->fetchAssoc("SELECT * FROM `{$consumerTable}` WHERE `id` = '{$api}' AND `{$consumerColumn}` = '{$creds['consumer_key']}'");
//
// 			$data = Utils::json_decode($creds['data']);
// 			$data->apiDetail = $apiDetail;
// 			$creds['data'] = json_encode($data);
// 		}

		return new Response($creds['data'], 200, [
			"Content-Type" => "application/json",
			"Access-Control-Allow-Origin" => "*",
			"Connection" => "close"
		]);
    }
}
