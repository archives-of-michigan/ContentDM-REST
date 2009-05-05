<?php

class Collection {
  public $alias;
  public $name;
  public $path;
  
  private $field_info;
  
  function __construct($item) {
    $this->alias = $item['alias'];
    $this->name = $item['name'];
    $this->path = $item['path'];
    
    $this->field_info();
  }
  
  function field_info() {
    if(!isset($this->field_info))
      $this->field_info = dmGetCollectionFieldInfo($this->alias);
    
    return $this->field_info;
  }
}