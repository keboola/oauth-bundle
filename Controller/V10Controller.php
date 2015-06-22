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
	public function testAction(Request $request)
	{
/**
 * @var \Doctrine\DBAL\Connection
 */
$conn = $this->getDoctrine()->getConnection('oauth_providers');
// var_dump($this->getDoctrine()->getConnectionNames());
$res = $conn->fetchAll('SELECT * FROM `consumers_v2`');
var_dump($res);
die();

        $conn = $this->getContainer()->get('doctrine.dbal.oauth_providers');

	}

	protected function getAuthenticateUrl($oauthToken)
	{
		return;
	}
}
