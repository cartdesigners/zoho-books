<?php

// Tested on PHP 5.2, 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('ZohoBooks needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('ZohoBooks needs the JSON PHP extension.');
}

// Zoho base
require(dirname(__FILE__) . '/ZohoBooks/ZohoBooks.php');