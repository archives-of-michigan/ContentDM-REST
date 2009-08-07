<?php

class Dir {
  public static function glob($path, $recursive = FALSE, $pattern = null) {
    # Dude! http://ruby-doc.org/core/classes/Dir.html#M002322
    $files = array();
    if($recursive) {
      $glob = new RecursiveDirectoryIterator($path);
      foreach(new RecursiveIteratorIterator($glob) as $filename => $cur) {
        if(($pattern && preg_match($pattern, $filename)) || !$pattern) {
          $files[] = $filename;
        }
      }
    } else {
      $files = glob($path);
      $files = ($files === FALSE) ? array() : $files;
    }
    
    return $files;
  }
}