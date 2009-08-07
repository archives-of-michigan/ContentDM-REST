<?php

class URLParser {
  public static function parse_uri($query_string) {
    $query_parts = URLParser::split_query_string($query_string);
    return $query_parts['uri'];
  }
  
  public static function parse_query_string($query_string) {
    $query_parts = URLParser::split_query_string($query_string);
    return $query_parts['query_string'];
  }
  
  private static function split_query_string($query_string) {
    $query_string = preg_replace('/\?/', '', $query_string);
    
    $uri_and_params = array();
    
    $parts = split('&', $query_string);
    $params = array();
    $uri = '/';
    $param_strings = array();
    foreach($parts as $part) {
      $keyval = split('=',$part);
      $name = $keyval[0];
      $value = (count($keyval) == 2) ? $keyval[1] : '';
      if($name == 'q') {
        $uri = ($value == '') ? '/' : $value;
      } else {
        $params[$name] = $value;
        $param_strings[] = $name.'='.$value;
      }
    }
    $uri_and_params['uri'] = $uri;
    $uri_and_params['params'] = $params;
    $uri_and_params['query_string'] = join('&',$param_strings);
    
    return $uri_and_params;
  }
}