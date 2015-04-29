<?php

/**
 * Test the probe() produces a properly functioning SSL configuration
 */
abstract class CA_Config_TestBase extends PHPUnit_Framework_TestCase {
    const VALID_SSL_URL = 'https://drupal.org/INSTALL.mysql.txt';
    const INVALID_SSL_URL = 'https://www-test.civicrm.org:4433/index.html';

    /**
     * Generate a CA configuration
     *
     * @see CA_Config_Curl::probe, CA_Config_Stream::probe
     * @return object
     */
    abstract function probe($params = array());

    /**
     * Perform an HTTP request with the CA config
     *
     * @param string $url
     * @param object $caConfig
     * @return mixed NULL or FALSE for a failed request
     */
    abstract function get($url, $caConfig);

    public function testValidUrl_Default() {
        $caConfig = $this->probe();
        $this->assertTrue($caConfig->isEnableSSL(), 'SSL should be enabled');
        $response = $this->get(self::VALID_SSL_URL, $caConfig);
        $this->assertNotEmpty($response, sprintf('Valid HTTPS URL (%s) should produce valid response data (Policy: %s)', self::VALID_SSL_URL, var_export($caConfig, TRUE)));
    }

    public function testValidUrl_noVerify() {
        $caConfig = $this->probe(array(
            'verify_peer' => FALSE,
        ));
        $this->assertTrue($caConfig->isEnableSSL(), 'SSL should be enabled');
        $response = $this->get(self::VALID_SSL_URL, $caConfig);
        $this->assertNotEmpty($response, sprintf('Valid HTTPS URL (%s) without verification should produce valid response data (Policy: %s)', self::VALID_SSL_URL, var_export($caConfig, TRUE)));
    }

    public function testInvalidUrl_Default() {
        $caConfig = $this->probe();
        $this->assertTrue($caConfig->isEnableSSL(), 'SSL should be enabled');
        $response = $this->get(self::INVALID_SSL_URL, $caConfig);
        $this->assertEmpty($response, sprintf('Invalid HTTPS URL (%s) should produce invalid response data (Policy: %s)', self::INVALID_SSL_URL, var_export($caConfig, TRUE)));
    }

    public function testInvalidUrl_noVerify() {
        $caConfig = $this->probe(array(
            'verify_peer' => FALSE,
        ));
        $this->assertTrue($caConfig->isEnableSSL(), 'SSL should be enabled');
        $response = $this->get(self::INVALID_SSL_URL, $caConfig);
        $this->assertNotEmpty($response, sprintf('Invalid HTTPS URL (%s) without verification should produce valid response data (Policy: %s)', self::INVALID_SSL_URL, var_export($caConfig, TRUE)));
    }
}
