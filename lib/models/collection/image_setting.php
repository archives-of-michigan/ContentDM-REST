<?php
require_once 'content_dm.php';

class CollectionImageSetting {
  public $zoom_levels;
  public $pan_enabled;
  public $min_dimensions_for_pan;
  
  function __construct($pan_enabled, $min_dimensions_for_pan, $zoom_levels) {
    $this->pan_enabled = $pan_enabled;
    $this->min_dimensions_for_pan = $min_dimensions_for_pan;
    $this->zoom_levels = $zoom_levels;
  }
  
  public static function find_by_alias($alias) {
    $pan_enabled = null;
    $min_dimensions_for_pan = null;
    $default_zoom_levels = null;
    $maxderivedimg = null;
    $viewer = null;
    $docviewer = null;
    
    dmGetCollectionImageSettings($alias, $pan_enabled, $min_dimensions_for_pan, 
      $default_zoom_levels, $maxderivedimg, $viewer, $docviewer);
    
    $zoom_levels = CollectionImageSetting::calculate_zoom_levels($default_zoom_levels, $docviewer);
    return new CollectionImageSetting($pan_enabled, $min_dimensions_for_pan, $zoom_levels);
  }
  
  public static function calculate_zoom_levels($zoom_levels, $viewer_sizes) {
    return null;
  }
}