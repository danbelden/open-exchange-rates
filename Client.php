<?php

/**
 * [CLASS SHORT DESCRIPTION]
 *
 * @category   Symfony2
 * @package    REPLICADA
 * @author     Daniel Belden <me@danbelden.com>
 */
namespace OpenExRt;

/**
 *
 */
class Client
{
    // Client constants
    const API_URL_ENDPOINT = 'http://openexchangerates.org/api/';
    const API_FILE_LATEST = 'latest.json';
    const API_FILE_CURRENCIES = 'currencies.json';
    const API_FOLDER_HISTORICAL = 'historical';
    const OPTION_APP_ID = 'appId';
    
    /**
     * The App ID required to enable connectivity
     * @see https://openexchangerates.org/documentation#app-ids
     * @var string
     */
    private $appId;

    /**
     * Client constructor method to initialise the client
     * 
     * @param array $options
     */
    public function __construct($options = array())
    {
        // If the app Id is provided to the constructor, set it
        if (isset($options[self::OPTION_APP_ID])) {
            $this->setAppId($options[self::OPTION_APP_ID]);
        }
    }

    /**
     * Getter method to retrieve the configured App ID
     *
     * @see https://openexchangerates.org/documentation#app-ids
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * Setter method to configure the client App ID
     * - Login to your account to retrieve your App Id
     *
     * @see https://openexchangerates.org/documentation#app-ids
     * @param string $apiToken
     * @return OpenExchange\Client
     */
    public function setAppId($appId)
    {
        // Set the app ID provided
        $this->appId = $appId;

        // Enable method chaining by returning this
        return $this;
    }

    /**
     * Method to retrieve the latest currency rate information
     *
     * @see https://openexchangerates.org/documentation#accessing-the-api
     * @return stdClass
     */
    public function getLatest()
    {
        // Form the url to the API for the request
        $apiUrl = self::API_URL_ENDPOINT . self::API_FILE_LATEST .
            '?app_id=' . $this->getAppId();

        // Retrieve the API response in decoded object form
        return $this->getCurlResponse($apiUrl);
    }

    /**
     * Method to retrieve the currency list available from the API
     *
     * @see https://openexchangerates.org/documentation#accessing-the-api
     * @return stdClass
     */
    public function getCurrencies()
    {
        // Form the url to the API for the request
        $apiUrl = self::API_URL_ENDPOINT . self::API_FILE_CURRENCIES .
            '?app_id=' . $this->getAppId();

        // Retrieve the API response in decoded object form
        return $this->getCurlResponse($apiUrl);
    }

    /**
     * Method to retrieve the historical rate information for a given date
     *
     * @see https://openexchangerates.org/documentation#historical-data
     * @param \DateTime $date
     * @return stdClass
     */
    public function getHistorical(\DateTime $date)
    {
        // Form the url to the API for the request
        $apiUrl = self::API_URL_ENDPOINT . $this->getHistoricalFilePath($date) .
            '?app_id=' . $this->getAppId();

        // Retrieve the API response in decoded object form
        return $this->getCurlResponse($apiUrl);
    }

    /**
     * Helper function to retrieve the relative file path to historical rate
     * json files on the open exchange server.
     *
     * @param \DateTime $date
     * @return string
     */
    private function getHistoricalFilePath(\DateTime $date)
    {
        return self::API_FOLDER_HISTORICAL . '/' .
            $date->format('Y-m-d') . '.json';
    }

    /**
     * Helper method to perform a cURL request to the API for a given URL's data
     * 
     * @param string $url
     * @return stdClass
     */
    private function getCurlResponse($url)
    {
        // Open CURL session ensuring the correct headers are configured
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Retrieve the JSON response from the API
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response and return it
        return json_decode($json);
    }
}
