<?php
/**
 * https://developer.etrade.com/ctnt/dev-portal/getContent?contentUri=V0_Documentation-GettingStarted
 */

namespace Zechdc\OAuth1\Client\Server;

use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;
use League\OAuth1\Client\Server\User;
use Mockery\Exception;

class Etrade extends Server
{
    protected $responseType = 'json';

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
}
