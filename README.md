OpenExRt\Client
========

A PHP 5, wrapper for the (https://openexchangerates.org)[openexchangerates.org] API.

## Usage
The following subsections will describe how each method of the wrapper class, and how it can be leveraged to retrieve forex rates and currencies.
To use the class, you will need an APP ID, this is provided by openexchangerates.org when you sign-up and register for an account!

### Constructor
First start by creating the client object, with the standard constructor use:
```php
$client = new \OpenExRt\Client();
```

### setAppId
This method enables you to set your personal API key (AppId) on the client, to grant access to the forex rates.
Simply call the setAppId method with your App Id:
```php
$client->setAppId('Insert App Id Here');
```
App Id can also be set in the constructor via the optional options array, like so:
```php
$client = new \OpenExRt\Client(array(
    \OpenExRt\Client::OPTION_APP_ID => 'Insert App Id Here'
));
```
The constructor parameter will use the same setter method inside the class.