<?php
require_once 'content_dm.php';
require_once file_join('models','collection','field.php');

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
    $this->initialize_image_settings();
  }
  
  private function initialize_image_settings() {
    $pan_enabled = null;
    $minjpegdim = null;
    $zoom_levels = null;
    $maxderivedimg = null;
    $viewer = null;
    $docviewer = null;
    
    dmGetCollectionImageSettings($this->alias, $pan_enabled, $minjpegdim, $zoom_levels, $maxderivedimg, $viewer, $docviewer);
    
    $zoom_settings = $this->calculate_zoom_levels($zoom_levels, $viewer);
    
    $this->image_settings = array(
      'zoom' => $zoom_settings,
      'pan_enabled' => $pan_enabled,
      'min_dimensions_for_pan' => $minjpegdim
    );
  }
  
  private function calculate_zoom_levels($zoom_levels, $viewer_sizes) {
    
  }
  
  public static function all() {
    $results = array();
    
    $dm_collections = dmGetCollectionList();
    foreach($dm_collections as $collection) {
      $results[] = new Collection($collection);
    }
    return $results;
  }
}