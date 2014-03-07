<?php

/**
 * docblock here
 */
namespace OpenExRt\Tests;

require_once '../Client.php';
use OpenExRt\Client;

/**
 * Test class to perform unit tests on the open exchange rates client
 * - Tests the constructor options set the relevant options passed
 * - Tests the set and get app Id methods configure the provided app id
 * - Tests the getCurrencies method retrieves the list of currencies and their codes
 * - Test the getLatest method returns an error when attempting with an invalid app id
 * - Tests the getLatest method returns a valid list of currency codes with rates
 * - Tests the getHistorical method returns an error when attempting with an invalid app id
 * - Tests the getHistorical method returns a valid lsit of currency codes with rates
 * - Tests the getHistorical method returns an error when attempting with a future date
 *
 * @category   OpenExRt
 * @package    OpenExRt
 * @subpackage Tests
 * @see https://openexchangerates.org
 * @author     Daniel Belden <me@danbelden.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    // Define your appIds
    const APP_ID_VALID = '768e02249db349abb7221558f622c8bd';
    const APP_ID_INVALID = 'danbelden.com';

    /**
     * The client with the valid app id
     * - Will set self::APP_ID_VALID on the client
     * @var OpenExRt\Client
     */
    private $validClient;

    /**
     * The client with the invalid app id
     * - Will set self::APP_ID_INVALID on the client
     * @var OpenExRt\Client
     */
    private $invalidClient;

    /**
     * Setup function to initialise two types of client for testing
     * - Valid client with a valid app id
     * - Invalid client with an invalid app id
     *
     * @return OpenExRt\Client
     */
    public function setUp()
    {
        // Create the valid client
        $this->validClient = new Client();
        $this->validClient->setAppId(self::APP_ID_VALID);

        // Create the invalid client
        $this->invalidClient = new Client();
        $this->invalidClient->setAppId(self::APP_ID_INVALID);
    }

    /**
     * Test method to ensure the constructor options array sets the relevant
     * settings inside the client
     * - Esnures app ids passed in the constructor are set
     */
    public function testConstructorOptions()
    {
        // Create the client constructor options array
        $options = array(Client::OPTION_APP_ID => self::APP_ID_INVALID);

        // Create the client passing in the options array
        $client = new Client($options);

        // Assert the set app id is the one provide dint he constructor
        $this->assertEquals(self::APP_ID_INVALID, $client->getAppId());
    }

    /**
     * Test method to perform checks on the setAppId() method of the client
     * - Ensures the response object is the client itself
     * - Ensures the set method sets the provided app Id
     */
    public function testSetAndGetAppId()
    {
        // Set the App Id on the client
        $responseClient = $this->invalidClient->setAppId(self::APP_ID_INVALID);

        // Assert the response from the set method is the client
        $this->assertInstanceOf('OpenExRt\Client', $responseClient);

        // Retrieve the set app Id
        $appId = $this->invalidClient->getAppId();

        // Assert the app Id is set correctly
        $this->assertEquals($appId, self::APP_ID_INVALID);
    }

    /**
     * Test method to perform tests on the response from the API for the
     * currencies supported by the Open Exchange API
     * - getCurrencies does not require a valid app id so using invalid client
     */
    public function testGetCurrencies()
    {
        // Retrieve the list of currencies
        $apiResponse = $this->invalidClient->getCurrencies();

        // Check the decoded response from the API is a standard class
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

    /**
     * Test method to perform an API call to the latest currency endpoint to
     * ensure a call with an invalid App Id returns the expected result structure.
     */
    public function testGetLatestWithInvalidAppId()
    {
        // Attempt to retrieve the latest currency rates
        $apiResponse = $this->invalidClient->getLatest();

        // Ensure the API response format is as expected
        $this->assertInvalidApiResponseFormat($apiResponse);
    }

    /**
     * Test method to perform an API call to the latest currency endpoint to
     * ensure a call with a valid App Id returns the expected result structure.
     */
    public function testGetLatestWithValidAppId()
    {
        // Attempt to retrieve the latest currency rates
        $apiResponse = $this->validClient->getLatest();

        // Ensure the API response format is as expected
        $this->assertValidApiRatesResponseFormat($apiResponse);
    }

    /**
     * Test method to perform an API call to the historical currency endpoint to
     * ensure a call with an invalid App Id returns the expected result structure.
     */
    public function testGetHistoricalWithInvalidAppId()
    {
        // Pick a historical date to query with [Todays date -7 days]
        $histDate = new \DateTime();
        $histDate->modify('-7 days');

        // Attempt to retrieve the historical currency rates
        $apiResponse = $this->invalidClient->getHistorical($histDate);

        // Ensure the API response format is as expected
        $this->assertInvalidApiResponseFormat($apiResponse);
    }

    /**
     * Test method to perform an API call to the historical currency endpoint to
     * ensure a call with a valid App Id returns the expected result structure.
     */
    public function testGetHistoricalWithValidAppId()
    {
        // Pick a historical date to query with [Todays date -7 days]
        $histDate = new \DateTime();
        $histDate->modify('-7 days');

        // Attempt to retrieve the historical currency rates
        $apiResponse = $this->validClient->getHistorical($histDate);

        // Ensure the API response format is as expected
        $this->assertValidApiRatesResponseFormat($apiResponse);
    }

    /**
     * Test method to perform an API call to the historical currency endpoint to
     * ensure a call with a valid App Id and invalid date, returns the expected
     * result structure.
     */
    public function testGetHistoricalWithInvalidDate()
    {
        // Pick a historical date to query with [Todays date -7 days]
        $histDate = new \DateTime();
        $histDate->modify('+7 days');

        // Attempt to retrieve the historical currency rates
        $apiResponse = $this->validClient->getHistorical($histDate);

        // Assert the response object was decoded object
        $this->assertInstanceOf('stdClass', $apiResponse);

        // Ensure the response contains an unavailable error
        $this->assertTrue($apiResponse->error);
        $this->assertEquals(400, $apiResponse->status);
        $this->assertEquals('not_available', $apiResponse->message);
        $this->assertEquals(
            'Historical rates for the requested date are not available - please try a different date, or contact support@openexchangerates.org.',
            $apiResponse->description
        );
    }

    /**
     * Helper method to perform the consistent assertions for the response from
     * the API once an invalid App ID is provided to a service that requires one.
     *
     * @param stdClass $apiResponse
     */
    private function assertInvalidApiResponseFormat($apiResponse)
    {
        // Assert the response object was decoded object
        $this->assertInstanceOf('stdClass', $apiResponse);

        // Ensure the response contains an invalid app id error
        $this->assertTrue($apiResponse->error);
        $this->assertEquals(401, $apiResponse->status);
        $this->assertEquals('invalid_app_id', $apiResponse->message);
        $this->assertEquals(
            'Invalid App ID provided - please sign up at https://openexchangerates.org/signup, or contact support@openexchangerates.org. Thanks!',
            $apiResponse->description
        );
    }

    /**
     * Helper method to perform the consistent assertions for the response from
     * the API once an valid App ID is provided to a service that requires one
     * and is expected to return a list of currency rates.
     *
     * @param stdClass $apiResponse
     */
    private function assertValidApiRatesResponseFormat($apiResponse)
    {
        // Assert the response object was decoded object
        $this->assertInstanceOf('stdClass', $apiResponse);

        // Ensure the response contains a disclaimer and license parameter
        $this->assertNotEmpty($apiResponse->disclaimer);
        $this->assertInternalType('string', $apiResponse->disclaimer);
        $this->assertNotEmpty($apiResponse->license);
        $this->assertInternalType('string', $apiResponse->license);

        // Ensure the response has a timestamp for when the rates calculated
        $this->assertInternalType('int', $apiResponse->timestamp);

        // Ensure the response has a base currency for which rates calculated
        $this->assertNotEmpty($apiResponse->base);
        $this->assertInternalType('string', $apiResponse->base);

        // Ensure the response has a rates param we can iterate through
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
