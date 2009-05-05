<?php

class Collection {
  public $alias;
  public $name;
  public $path
  
  function __construct($item) {
    $this->alias = $item['alias'];
    $this->name = $item['name'];
    $this->path = $item['path'];
  }
}