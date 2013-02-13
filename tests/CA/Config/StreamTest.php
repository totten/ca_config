<?php

/**
 * Test that Stream::probe() produces a properly functioning SSL configuration
 */
class CA_Config_StreamTest extends CA_Config_TestBase {
    public function probe($params = array()) {
      return CA_Config_Stream::probe($params);
    }

    public function get($url, $caConfig) {
        $context = stream_context_create(array(
            'ssl' => $caConfig->toStreamOptions(),
        ));
        try {
            return file_get_contents($url, 0, $context);
        } catch (Exception $e) {
            return NULL;
        }
    }
}