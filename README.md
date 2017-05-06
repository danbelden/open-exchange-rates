OpenExRt\Client
========

[![Build Status](https://travis-ci.org/danbelden/open-exchange-rates.svg?branch=master)](https://travis-ci.org/danbelden/open-exchange-rates)

A PHP 5, wrapper for the [openexchangerates.org](https://openexchangerates.org) API.

# Usage
The following subsections will describe how each method of the wrapper class are called, and how each one can be used to enable retrieve of forex rates from the OpenExchangeRates API.
To use the class, you will need an App Id, a unique string which gives you access to their API - this is provided by [openexchangerates.org](https://openexchangerates.org) when you sign-up and register for an account!

## Constructor
First start by creating the client object, with the standard constructor use:
```php
$client = new \OpenExRt\Client();
```

## setAppId($appId)
This method enables you to set your app Id on the client, to grant access to the forex rates.
Simply call the setAppId method with your App Id as follows:
```php
$client->setAppId('Insert App Id Here');
```
An App Id can also be set via the constructor options, like so:
```php
$client = new \OpenExRt\Client(array(
    \OpenExRt\Client::OPTION_APP_ID => 'Insert App Id Here'
));
```
The constructor parameter approach, will use the same setter method.


## getAppId()
You can retrieve the current App Id configured inside the client with the getAppId method, like so:
```php
$appId = $client->getAppId();
```

## getCurrencies()
You can retrieve the list of available currencies with the client by calling the getCurrencies method, like so:
```php
$apiResponse = $client->getCurrencies();
foreach ($apiResponse as $currencyCode => $currencyName) {
    echo $currencyCode . ': ' . $currencyName . PHP_EOL;
}
```
The response from the API will determine what object is returned and therefore, whether it is iterable.
At the moment this doc is written, the API does *NOT* require the AppId to retrieve the list of currencies.

## getLatest()
You can retrieve the list of latest currency rates (based on USD) with the  getLatest method, like so:
```php
// Retrieve the API response for the latest forex rates
$apiResponse = $client->getLatest();

// Retrieve the static sections of the API response
$baseCurrency = $apiResponse->base;
$timestampOfRates = $apiResponse->timestamp;

// Loop the set of forex rates
foreach ($apiResponse->rates as $currencyCode => $currencyRate) {
    echo $currencyCode . ': ' . $currencyRate . PHP_EOL;
}
```

## getHistorical($prevDate)
You can retrieve the historical rates for a previous given date with the getHistorical method, like so:
```php
// Determine the date you wish to retrieve rates for
$prevDate = new \DateTime();
$prevDate->setDate(2013, 1, 1);

// Retrieve the historical rates for the given day
$apiResponse = $client->getHistorical($prevDate);

// Loop the set of forex rates
foreach ($apiResponse->rates as $currencyCode => $currencyRate) {
    echo $currencyCode . ': ' . $currencyRate . PHP_EOL;
}
``` 

The response from the historical API is subject to valid app id, date and availability.
Please make sure you verify the result before trying to iterate it!

Enjoy.