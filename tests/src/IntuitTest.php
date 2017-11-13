<?php

namespace Wheniwork\OAuth1\Client\Test\Server;

use Wheniwork\OAuth1\Client\Server\Intuit;

use Mockery as m;

class IntuitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Close mockery.
     *
     * @return void
     */
    public function tearDown()
    {
        m::close();
    }

    public function testGettingUserDetails()
    {
        $server = m::mock(
            'Wheniwork\OAuth1\Client\Server\Intuit[createHttpClient,protocolHeader]',
            [$this->getMockClientCredentials()]
        );

        $temporaryCredentials = m::mock('League\OAuth1\Client\Credentials\TokenCredentials');
        $temporaryCredentials->shouldReceive('getIdentifier')->andReturn('tokencredentialsidentifier');
        $temporaryCredentials->shouldReceive('getSecret')->andReturn('tokencredentialssecret');

        $server->shouldReceive('createHttpClient')->andReturn($client = m::mock('stdClass'));

        $me = $this;
        $client->shouldReceive('get')->with('https://appcenter.intuit.com/api/v1/user/current', m::on(function($headers) use ($me) {
            $me->assertTrue(isset($headers['Authorization']));

            // OAuth protocol specifies a strict number of
            // headers should be sent, in the correct order.
            // We'll validate that here.
            $pattern = '/OAuth oauth_consumer_key=".*?", oauth_nonce="[a-zA-Z0-9]+", oauth_signature_method="HMAC-SHA1", oauth_timestamp="\d{10}", oauth_version="1.0", oauth_token="tokencredentialsidentifier", oauth_signature=".*?"/';

            $matches = preg_match($pattern, $headers['Authorization']);
            $me->assertEquals(1, $matches, 'Asserting that the authorization header contains the correct expression.');

            return true;
        }))->once()->andReturn($request = m::mock('stdClass'));

        $request->shouldReceive('send')->once()->andReturn($response = m::mock('stdClass'));
        $response->shouldReceive('xml')->once()->andReturn($this->getUserPayload());

        $user = $server
            ->getUserDetails($temporaryCredentials);
        $this->assertInstanceOf('League\OAuth1\Client\Server\User', $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals(null, $server->getUserUid($temporaryCredentials));
        $this->assertEquals('JohnDoe@g88.net', $server->getUserEmail($temporaryCredentials));
    }

    protected function getMockClientCredentials()
    {
        return array(
            'identifier' => $this->getApplicationKey(),
            'secret' => 'mysecret',
            'callback_uri' => 'http://app.dev/',
        );
    }

    protected function getAccessToken()
    {
        return 'lmnopqrstuvwxyz';
    }

    protected function getApplicationKey()
    {
        return 'abcdefghijk';
    }

    private function getUserPayload()
    {
        $user = <<<XML
<UserResponse xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://platform.intuit.com/api/v1">
    <ErrorCode>0</ErrorCode>
    <ServerTime>2012-04-13T18:47:34.5422493Z</ServerTime>
    <User>
        <FirstName>John</FirstName>
        <LastName>Doe</LastName>
        <EmailAddress>JohnDoe@g88.net</EmailAddress>
        <IsVerified>true</IsVerified>
    </User>
</UserResponse>
XML;
        return new \SimpleXMLElement($user);
    }
}
