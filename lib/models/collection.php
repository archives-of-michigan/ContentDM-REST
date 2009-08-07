<?php
require_once 'content_dm.php';
require_once 'models/collection/field.php';
require_once 'models/collection/image_setting.php';

class Collection {
  public $alias;
  public $name;
  public $path;
  
  public $fields;
  public $image_settings;
  
  function __construct($item) {
    $this->alias = $item['alias'];
    $this->name = $item['name'];
    $this->path = $item['path'];
    
    $this->fields = CollectionField::find_all_by_alias($this->alias);
    $this->image_settings = CollectionImageSetting::find_by_alias($this->alias);
  }
  
  public static function all() {
    $results = array();
    
    $dm_collections = dmGetCollectionList();
    foreach($dm_collections as $collection) {
      $results[] = new Collection($collection);
    }
    return $results;
  }
  
  public static function find($alias) {
    $alias = (preg_match('/^\//',$alias)) ? $alias : '/'.$alias;
    
    $found = dmGetCollectionParameters($alias, $name, $path);
    if($found == 0) {
      $collection = new Collection(array(
        'alias' => $alias,
        'name' => $name,
        'path' => $path
      ));
      return $collection;
    } else {
      return null;
    }
  }
}