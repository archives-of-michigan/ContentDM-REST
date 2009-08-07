<?php
require_once dirname(__FILE__).'/../test_helper.php';
require_once 'url_parser.php';

// URLParser.parse_uri
class lib_url_parser_parse_uri_test extends UnitTestCase {
  
  public function testShouldSetRequestUriForDefaultURL() {
    $this->assertEqual(
      URLParser::parse_uri(''),
      '/');
  }
  
  public function testShouldSetRequestUriForSlashURL() {
    $this->assertEqual(
      URLParser::parse_uri('/'),
      '/');
  }
  
  public function testShouldSetRequestUriForEmptyURL() {
    $this->assertEqual(
      URLParser::parse_uri('?q='),
      '/');
  }
  
  public function testShouldSetRequestUriForBasicURL() {
    $this->assertEqual(
      URLParser::parse_uri('?q=/collections/3/item/4'),
      '/collections/3/item/4');
  }
  
  public function testShouldSetRequestUriForURLWithQueryString() {
    $this->assertEqual(
      URLParser::parse_uri('?q=/collections/3/item/4&dream=theater&rainbow=rising'),
      '/collections/3/item/4');
  }
}

// URLParser.parse_query_string
class lib_url_parser_parse_query_string_test extends PHPUnit_Framework_TestCase {
  
  public function testShouldSetQueryStringForDefaultURL() {
    $this->assertEqual(
      URLParser::parse_query_string(''),
      '');
  }
  
  public function testShouldSetQueryStringForBasicURL() {
    $this->assertEqual(
      URLParser::parse_query_string('?q=/collections/3/item/4'),
      '');
  }
  
  public function testShouldSetQueryStringForURLWithQueryString() {
    $this->assertEqual(
      URLParser::parse_uri('?q=/collections/3/item/4&dream=theater&rainbow=rising'),
      'dream=theater&rainbow=rising');
  }
}