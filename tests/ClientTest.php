<?php

namespace OpenExRt\Tests;

use LogicException;
use OpenExRt\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $appId;

    public function setUp()
    {
        $this->appId = getenv('APP_ID');
        if (empty($this->appId)) {
            throw new LogicException('Undefined APP_ID environment variable');
        }
    }

    public function testConstructorOptions()
    {
        $options = array(Client::OPTION_APP_ID => 'Test');
        $client  = new Client($options);

        $this->assertEquals('Test', $client->getAppId());
    }

    public function testSetAndGetAppId()
    {
        $testAppId = 'test';

        $client = new Client();
        $client->setAppId($testAppId);

        $appId = $client->getAppId();

        $this->assertEquals($testAppId, $appId);
    }

    public function testGetCurrencies()
    {
        $client = new Client();

        $apiResponse = $client->getCurrencies();
        $this->assertInstanceOf('stdClass', $apiResponse);

        // Loop the response object to check it's integrity
        foreach($apiResponse as $currencyCode => $currencyName) {

            // Assert the currency code and values are none-empty strings
            $this->assertNotEmpty($currencyCode);
            $this->assertInternalType('string', $currencyCode);
            $this->assertNotEmpty($currencyName);
            $this->assertInternalType('string', $currencyName);
        }
    }

    public function testGetLatestWithInvalidAppId()
    {
        $client = new Client();
        $client->setAppId('test');

        $apiResponse = $client->getLatest();

        $this->assertInvalidApiResponseFormat($apiResponse);
    }

    public function testGetLatestWithValidAppId()
    {
        $client = new Client();
        $client->setAppId($this->appId);

        $apiResponse = $client->getLatest();

        $this->assertValidApiRatesResponseFormat($apiResponse);
    }

    public function testGetHistoricalWithInvalidAppId()
    {
        $histDate = new \DateTime();
        $histDate->modify('-7 days');

        $client = new Client();
        $client->setAppId('test');

        $apiResponse = $client->getHistorical($histDate);

        $this->assertInvalidApiResponseFormat($apiResponse);
    }

    public function testGetHistoricalWithValidAppId()
    {
        $histDate = new \DateTime();
        $histDate->modify('-7 days');

        $client = new Client();
        $client->setAppId($this->appId);

        $apiResponse = $client->getHistorical($histDate);

        $this->assertValidApiRatesResponseFormat($apiResponse);
    }

    public function testGetHistoricalWithInvalidDate()
    {
        $histDate = new \DateTime();
        $histDate->modify('+7 days');

        $client = new Client();
        $client->setAppId($this->appId);

        $apiResponse = $client->getHistorical($histDate);

        $this->assertInstanceOf('stdClass', $apiResponse);

        $this->assertTrue($apiResponse->error);
        $this->assertEquals(400, $apiResponse->status);
        $this->assertEquals('not_available', $apiResponse->message);
        $this->assertEquals(
            'Historical rates for the requested date are not available - please try a different date, or contact support@openexchangerates.org.',
            $apiResponse->description
        );
    }

    private function assertInvalidApiResponseFormat($apiResponse)
    {
        $this->assertInstanceOf('stdClass', $apiResponse);

        $this->assertTrue($apiResponse->error);
        $this->assertEquals(401, $apiResponse->status);
        $this->assertEquals('invalid_app_id', $apiResponse->message);
        $this->assertEquals(
            'Invalid App ID provided. Please sign up at https://openexchangerates.org/signup, or contact support@openexchangerates.org.',
            $apiResponse->description
        );
    }

    private function assertValidApiRatesResponseFormat($apiResponse)
    {
        $this->assertInstanceOf('stdClass', $apiResponse);

        $this->assertNotEmpty($apiResponse->disclaimer);
        $this->assertInternalType('string', $apiResponse->disclaimer);
        $this->assertNotEmpty($apiResponse->license);
        $this->assertInternalType('string', $apiResponse->license);

        $this->assertInternalType('int', $apiResponse->timestamp);

        $this->assertNotEmpty($apiResponse->base);
        $this->assertInternalType('string', $apiResponse->base);

        $this->assertInstanceOf('stdClass', $apiResponse->rates);

        // Loop the set of provided rates
        foreach ($apiResponse->rates as $currencyCode => $currencyRate) {

            // Assert the currency code is none-empt string
            $this->assertNotEmpty($currencyCode);
            $this->assertInternalType('string', $currencyCode);

            // Ensure the currency rate is numeric
            $this->assertTrue(is_numeric($currencyRate));
        }
    }
}
