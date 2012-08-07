<?php

/**
 * @file
 *
 * Test that probe() produces a properly functioning SSL configuration
 * on the local machine.
 *
 * This is written as a standalone tool so it can be quickly installed and
 * run on new environments -- the whole point is to run it in a variety of
 * environments.
 */

define('VALID_SSL_URL', 'https://drupal.org/INSTALL.mysql.txt');
define('INVALID_SSL_URL', 'https://www.openca.org/developers.shtml');

require_once dirname(__FILE__) . '/../src/CA/Config/Stream.php';
require_once dirname(__FILE__) . '/../src/CA/Config/Curl.php';

## Default policy
$caConfig = CA_Config_Stream::probe();
if ($caConfig->isEnableSSL()) {
    test_run('default', 'test_stream_get', VALID_SSL_URL, $caConfig, TRUE);
    test_run('default', 'test_stream_get', INVALID_SSL_URL, $caConfig, FALSE);
} else {
    printf("Stream: This system does not support SSL!\n");
}

$caConfig = CA_Config_Curl::probe();
if ($caConfig->isEnableSSL()) {
    test_run('default', 'test_curl_get', VALID_SSL_URL, $caConfig, TRUE);
    test_run('default', 'test_curl_get', INVALID_SSL_URL, $caConfig, FALSE);
} else {
    printf("CURL: This system does not support SSL!\n");
}

## No-verify policy
$caConfig = CA_Config_Stream::probe(array('verify_peer' => FALSE));
if ($caConfig->isEnableSSL()) {
    test_run('no_verify', 'test_stream_get', VALID_SSL_URL, $caConfig, TRUE);
    test_run('no_verify', 'test_stream_get', INVALID_SSL_URL, $caConfig, TRUE);
} else {
    printf("Stream: This system does not support SSL!\n");
}

$caConfig = CA_Config_Curl::probe(array('verify_peer' => FALSE));
if ($caConfig->isEnableSSL()) {
    test_run('no_verify', 'test_curl_get', VALID_SSL_URL, $caConfig, TRUE);
    test_run('no_verify', 'test_curl_get', INVALID_SSL_URL, $caConfig, TRUE);
} else {
    printf("CURL: This system does not support SSL!\n");
}

###########################################################################

function test_run($policy, $handler, $url, $config, $expected) {
    //print("\n\n=== $handler ($url): Begin\n");
    $response = $handler($url, $config);
    $has_response = !empty($response) ? 'Y' : 'N';
    $ok = ($expected == !empty($response)) ? 'Y' : 'N';
    print("=== ok=$ok policy=$policy handler=$handler url=($url) has_response=$has_response\n");
}

/**
 * Perform a download with PHP stream handler
 *
 * @return string, document data
 */
function test_stream_get($url, CA_Config_Stream $caConfig) {
    $context = stream_context_create(array(
        'ssl' => $caConfig->toStreamOptions(),
    ));
    return file_get_contents($url, 0, $context);
}

/**
 * Perform a download with curl
 *
 * @return string, document data
 */
function test_curl_get($url, CA_Config_Curl $caConfig) {
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
