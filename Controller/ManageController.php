<?php

namespace Keboola\OAuthBundle\Controller;

use	Keboola\Syrup\Controller\ApiController,
	Keboola\Syrup\Exception\UserException;
use	Symfony\Component\HttpFoundation\Response,
	Symfony\Component\HttpFoundation\JsonResponse,
	Symfony\Component\HttpFoundation\Request;
use	Keboola\Utils\Utils;

class ManageController extends ApiController
{
	public function getAction($api, $id, Request $request)
	{
		$token = $this->storageApi->verifyToken();

		/**
		 * @var \Doctrine\DBAL\Connection
		 */
		$conn = $this->getConnection();

		$creds = $conn->fetchAssoc("SELECT `data`, `description`, `consumer_key`, `oauth_version`, `creator` FROM `credentials` WHERE `project` = '{$token['owner']['id']}' AND `id` = '{$id}' AND `api` = :api", ['api' => $api]);

		if (empty($creds['data'])) {
			throw new UserException("No data found for api: {$api} with id: {$id} in project {$token['owner']['name']}");
		}

		// TODO makes consumer_secret available to anyone with a token, is that quite desirable?
		// It's needed for OAuth 1.0 clients :-/
// 		$content = Utils::json_decode($request->getContent());
// 		if (!empty($content->includeApiDetail)) {
// 			$consumerTable = $creds['oauth_version'] == '2.0' ? 'consumers_v2' : 'consumers_v1';
// 			$consumerColumn = $creds['oauth_version'] == '2.0' ? 'client_id' : 'consumer_key';
// 			$apiDetail = $conn->fetchAssoc("SELECT * FROM `{$consumerTable}` WHERE `id` = '{$api}' AND `{$consumerColumn}` = '{$creds['consumer_key']}'");
//
// 			$data = Utils::json_decode($creds['data']);
// 			$data->apiDetail = $apiDetail;
// 			$creds['data'] = json_encode($data);
// 		}
		return new JsonResponse($creds, 200, [
			"Content-Type" => "application/json",
			"Access-Control-Allow-Origin" => "*",
			"Connection" => "close"
		]);
	}

	public function deleteAction($api, $id)
	{
		$token = $this->storageApi->verifyToken();

		if (empty($token['admin'])) {
			throw new UserException("Forbidden: Only project admin can delete existing credentials.");
		}

		$conn = $this->getConnection();

		// A check for delete rights would come here..IF WE HAD ONE!

		$result = $conn->delete('credentials', [
			'project' => $token['owner']['id'],
			'id' => $id,
			'api' => $api
		]);

		if ($result == 1) {
			return new Response(null, 204, [
				"Content-Type" => "application/json",
				"Access-Control-Allow-Origin" => "*",
				"Connection" => "close"
			]);
		} else {
			throw new UserException("Error deleting credentials for api: {$api} with id: {$id} in project {$token['owner']['name']}");
		}
	}

	/**
	 *
	 */
	public function listAction()
	{
		$token = $this->storageApi->verifyToken();

		$conn = $this->getConnection();

		$v1 = $this->getConsumers(1);
		$v2 = $this->getConsumers(2);

		$consumers = [];
		foreach($v1 as $consumer) {
			$consumers[] = [
				'id' => $consumer['id'],
				'friendly_name' => $consumer['friendly_name'],
				'client' => $consumer['consumer_key'],
				'oauth_version' => '1.0'
			];
		}

		foreach($v2 as $consumer) {
			$consumers[] = [
				'id' => $consumer['id'],
				'friendly_name' => $consumer['friendly_name'],
				'client' => $consumer['client_id'],
				'oauth_version' => '2.0'
			];
		}

		return new JsonResponse($consumers, 200, [
			"Content-Type" => "application/json",
			"Access-Control-Allow-Origin" => "*",
			"Connection" => "close"
		]);
	}

	protected function getConsumers($oauthVersion)
	{
		$table = $oauthVersion == 1 ? 'consumers_v1' : 'consumers_v2';

		return $this->getConnection()->fetchAll("SELECT * FROM `{$table}`");
	}

	/**
	 * @return \Doctrine\DBAL\Connection
	 */
	protected function getConnection()
	{
		return $this->getDoctrine()->getConnection('oauth_providers');
	}

	protected function verifyAdmin()
	{
		$token = $this->storageApi->verifyToken();

		$allowedOrgs = (array) $this->getParameter('manager_organizations');

		if (!in_array($token['organization']['id'], $allowedOrgs) || !$token['canManageTokens']) {
			throw new UserException("Only authorized Keboola Developers can manage consumer settings!");
		}

		return $token;
	}
}
