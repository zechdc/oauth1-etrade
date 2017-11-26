<?php
/**
 * https://developer.etrade.com/ctnt/dev-portal/getContent?contentUri=V0_Documentation-GettingStarted
 */

namespace Zechdc\OAuth1\Client\Server;

use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use League\OAuth1\Client\Signature\SignatureInterface;
use Mockery\Exception;
use League\OAuth1\Client\Credentials\TemporaryCredentials;

class Etrade extends Server
{
    protected $responseType = 'json';
    protected $defaultClientConfig = [
      'callback_uri' => 'oob'
    ];

    public function __construct($clientCredentials, SignatureInterface $signature = null)
    {
      $clientCredentials = array_replace($this->defaultClientConfig, $clientCredentials);
      parent::__construct($clientCredentials, $signature);
    }

    public function urlTemporaryCredentials()
    {
        return 'https://etws.etrade.com/oauth/request_token';
    }

    public function urlAuthorization()
    {
        return 'https://us.etrade.com/e/t/etws/authorize';
    }

    public function urlTokenCredentials()
    {
        return 'https://etws.etrade.com/oauth/access_token';
    }

    public function urlUserDetails()
    {
        throw new Exception("etrade does not provide a user details API");
    }

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        throw new Exception("etrade does not provide a user details API");
    }

    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        throw new Exception("etrade does not provide a user details API");
    }

    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        throw new Exception("etrade does not provide a user details API");
    }

    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        throw new Exception("etrade does not provide a user details API");
    }

    /**
     * Overwrite method to add key and token as params for etrade
     *
     * @param TemporaryCredentials|string $temporaryIdentifier
     * @return string
     */
    public function getAuthorizationUrl($temporaryIdentifier)
    {
      // Somebody can pass through an instance of temporary
      // credentials and we'll extract the identifier from there.
      if ($temporaryIdentifier instanceof TemporaryCredentials) {
        $temporaryIdentifier = $temporaryIdentifier->getIdentifier();
      }

      $parameters = ['key' => $this->clientCredentials->getIdentifier(),
        'token' => $temporaryIdentifier];

      $url = $this->urlAuthorization();
      $queryString = http_build_query($parameters);

      return $this->buildUrl($url, $queryString);
    }

  /**
     * Had to overwrite the method so we include the oauth_verifier
     * in the headers instead of body as etrade requires
     *
     * @param TemporaryCredentials $temporaryCredentials
     * @param string $temporaryIdentifier
     * @param string $verifier
     * @return TokenCredentials|void
     */
    public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verifier)
    {
      if ($temporaryIdentifier !== $temporaryCredentials->getIdentifier()) {
        throw new \InvalidArgumentException(
          'Temporary identifier passed back by server does not match that of stored temporary credentials.
                  Potential man-in-the-middle.'
        );
      }

      $uri = $this->urlTokenCredentials();
      $bodyParameters = array('oauth_verifier' => $verifier);


      $client = $this->createHttpClient();

      $headers = $this->getHeaders($temporaryCredentials, 'POST', $uri, $bodyParameters);

      //This is the wonky bit, we append oauth_verifier to the Authorization header
      $headers['Authorization'] = $headers['Authorization'] . ', oauth_verifier="' . $verifier . '"';

      try {
        $response = $client->post($uri, [
          'headers' => $headers
        ]);
      } catch (BadResponseException $e) {
        return $this->handleTokenCredentialsBadResponse($e);
      }

      return $this->createTokenCredentials((string) $response->getBody());
    }
}
