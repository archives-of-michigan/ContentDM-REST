<?php
class Collection extends Adapter {
  function dispatch() {
    
  }
  
  function select() {
    if (isset($options[TONIC_FIND_EXACT]) && $options[TONIC_FIND_EXACT]) {
      $self->select_all();
    } else {
      $self->select_single($_GET[''])
    }
  }
  
  function select_all() {
    $dm_collections = dmGetCollectionList();
  }
  
  function select_single($alias) {
    if($collection = $self->find($self->select_all(), $alias)) {
      return $collection;
    } else {
      return(FALSE);
    }
  }
  
  function find($collections, $alias) {
    $alias = preg_replace('/^\/','',$alias);
    foreach($collections as &$collection) {
      if($item['alias'] == $alias) {
        return($item);
      }
    }
    
    return(FALSE);
  }
}
?>