<?
function dmGetCollectionList() {
  return array(
    array(
      "alias" => "/p4006coll2",
      "name" => "Governors of Michigan",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll2"
    ),
    array(
      "alias" => "/p4006coll3",
      "name" => "Civil War Photographs",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll3"
    ),
    array(
      "alias" => "/p4006coll7",
      "name" => "Lighthouses and Life-Saving Stations",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll7"
    ),
    array(
      "alias" => "/p4006coll4",
      "name" => "Early Photography",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll4"
    ),
    array(
      "alias" => "/p4006coll5",
      "name" => "Sheet Music",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll5"
    ),
    array(
      "alias" => "/p4006coll8",
      "name" => "Main Streets",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll8"
    ),
    array(
      "alias" => "/p4006coll10",
      "name" => "Architecture",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll10"
    ),
    array(
      "alias" => "/p4006coll15",
      "name" => "Civil War Service Records",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll15"
    ),
    array(
      "alias" => "/p4006coll17",
      "name" => "Oral Histories",
      "path" => "D:\\Sites\\129401\\Data\\p4006coll17"
    ),
    array(
      "alias" => "/p129401coll0",
      "name" => "WPA Property Inventories",
      "path" => "D:\\Sites\\129401\\data\\p129401coll0"
    ),
    array(
      "alias" => "/p129401coll3",
      "name" => "Maps",
      "path" => "D:\\Sites\\129401\\data\\p129401coll3"
    ),
    array(
      "alias" => "/p129401coll7",
      "name" => "Death Records, 1897-1920",
      "path" => "D:\\Sites\\129401\\data\\p129401coll7_1"
    ),
    array(
      "alias" => "/p129401coll10",
      "name" => "Michigan Polish Americans",
      "path" => "D:\\Sites\\129401\\data\\p129401coll10"
    ) 
  );
}

function dmQuery() {
  return array();
}

function dmGetCollectionFieldInfo($alias) {
  return(
    array(
      array(
        'nick' => 'title',
        'name' => 'Title',
        'size' => '50',
        'search' => '1',
        'hide' => '0'
      )
    )
  );
}

function dmGetCollectionImageSettings($alias, &$pan_enabled, &$minjpegdim, &$zoom_levels, &$maxderivedimg, &$viewer, &$docviewer) {
  $pan_enabled = 1;
  $minjpegdim = 1024;
  $zoom_levels = array();
  $maxderivedimg = array();
  $viewer = array();
  $docviewer = array();
}

?>