# Intuit Provider for OAuth 1.0 Client

[![Build Status](https://img.shields.io/travis/wheniwork/oauth1-intuit.svg)](https://travis-ci.org/wheniwork/oauth1-intuit)
[![Code Coverage](https://img.shields.io/coveralls/wheniwork/oauth1-intuit.svg)](https://coveralls.io/r/wheniwork/oauth1-intuit)
[![Code Quality](https://img.shields.io/scrutinizer/g/wheniwork/oauth1-intuit.svg)](https://scrutinizer-ci.com/g/wheniwork/oauth1-intuit/)
[![License](https://img.shields.io/packagist/l/wheniwork/oauth1-intuit.svg)](https://github.com/wheniwork/oauth1-intuit/blob/master/LICENSE)
[![Latest Stable Version](https://img.shields.io/packagist/v/wheniwork/oauth1-intuit.svg)](https://packagist.org/packages/wheniwork/oauth1-intuit)

This package provides Intuit OAuth 1.0 support for the PHP League's [OAuth 1.0 Client](https://github.com/thephpleague/oauth1-client).

## Installation

To install, use composer:

```
composer require wheniwork/oauth1-intuit
```

## Usage

Usage is the same as The League's OAuth client, using `Wheniwork\OAuth1\Client\Server\Intuit` as the provider.

```php
$server = new Wheniwork\OAuth1\Client\Server\Intuit(array(
    'identifier'   => 'your-identifier',
    'secret'       => 'your-secret',
    'callback_uri' => 'http://your-callback-uri/',
));
```
