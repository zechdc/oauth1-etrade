# Etrade Provider for OAuth 1.0 Client

This package provides Etrade OAuth 1.0 support for the PHP League's [OAuth 1.0 Client](https://github.com/thephpleague/oauth1-client).

## Installation

To install, use composer:

```
composer require zechdc/oauth1-etrade
```

## Usage

Usage is the same as The League's OAuth client, using `Zechdc\OAuth1\Client\Server\Etrade` as the provider.

You must contact Etrade to setup your callback URL.

```php
//Step 1, setup an Etrade instance
$server = new Zechdc\OAuth1\Client\Server\Etrade(array(
    'identifier'   => 'oauth_customer_key',
    'secret'       => 'consumer_secret',
));

//Step 2, get create your Request Token ($temporaryCredentials)
public function getRequestTokenAndAuthorizeApplication(){

    //This creates your Request Token
    $temporaryCredentials = $this->server->getTemporaryCredentials();
    
    //Save the $temporaryCredentials in a session or DB to be used later.
    Session::set('temporary_credentials', $temporaryCredentials);
    
    //This will allow the user to Authorize your Application. It will redirect the user
    //to etrade. After they login and accept your application, it will either
    // 1) Redirect to your website - this requires you to contact etrade customer support and setup a callback url
    // 2) Etrade will show you a code called the oauth_verifier which you can manually copy into the next step.
    $this->server->authorize($temporaryCredentials);
}

//Step 3, use the request token ($temporaryCredentials) and the oauth_verifier provided by etrade to create your Access Token
public function getAccessToken(){
    //Get our temporary credentials from our storage
    $temporaryCredentials = Session::get('temporary_credentials');
    $requestToken = $temporaryCredentials->getIdentifier();
    
    //The code the user received after authorizing the application.
    $oauthVerifier = $_GET['oauth_verifier'];
    
    //This gets our Access Token
    $tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $requestToken, $oauthVerifier);
    
    //Save the Access Token so we can make and authorize more API calls. 
    Session::set('token_credentials', $tokenCredentials);
}

//Step 4, now that you have your Access Token, lets call an endpoint
public function getMarketData(){
    $client = new Guzzle\Client();
    $accessToken = Session::get('token_credentials');
    $url = "https://etwssandbox.etrade.com/market/sandbox/rest/quote/GOOGL.json";
    $method = 'GET';
    $params = ['detailFlag' => 'FUNDAMENTAL'];
    
    //This constructs our Authorization header and the oauth signature.
    $headers = $this->server->getHeaders($accessToken, $method, $url, $params);
  
    $res = $client->request($method, $url, [
      'headers' => $headers,
      'query' => $params
    ]);
    
    echo $res->getStatusCode();
    echo $res->getBody();
}
```