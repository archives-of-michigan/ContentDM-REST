<?php
class CollectionsGateway {
  
  function all() {
    $dm_collections = dmGetCollectionList();
    $results = array();
    foreach($dm_collections as $collection) {
      $results[] = new Collection($collection);
    }
    return $results;
  }
  function find_by_alias($alias) {
    $all_collections = $this->all();
    $found_collection = FALSE;
    foreach($all_collections as $collection) {
      if($collection->alias == $alias) {
        $found_collection = $collection;
      }
    }
    
    return $found_collection;
  }
}
?>