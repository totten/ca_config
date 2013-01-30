<?php

/**
 * Test that Curl::probe() produces a properly functioning SSL configuration
 */
class CA_Config_CurlTest extends CA_Config_TestBase {
    const VALID_SSL_URL = 'https://drupal.org/INSTALL.mysql.txt';
    const INVALID_SSL_URL = 'https://self-signed.onebitwise.com:4443/';

    function probe($params = array()) {
      return CA_Config_Curl::probe($params);
    }

    function get($url, $caConfig) {
        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt_array($ch, $caConfig->toCurlOptions());

        // grab URL and pass it to the browser
        $response = curl_exec($ch);

        // close cURL resource, and free up system resources
        curl_close($ch);
        return $response;
    }
}
