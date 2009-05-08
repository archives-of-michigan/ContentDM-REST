<?php
require_once 'content_dm.php';

class CollectionField {
  public $nick;
  public $name;
  public $size;
  public $search;
  public $hide;
  
  function __construct($info) {
    $this->nick = $info['nick'];
    $this->name = $info['name'];
    $this->size = $info['size'];
    $this->search = $info['search'];
    $this->hide = $info['hide'];
  }
  
  public static function find_all_by_alias($alias) {
    $records = array();
    
    $fields = dmGetCollectionFieldInfo($alias);
    
    foreach($fields as $field) {
      $records[] = new CollectionField($field);
    }
    
    return $records;
  }
}