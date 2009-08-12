<?php
require_once 'models/collection.php';

class components_collections_Entity extends k_Component {
  
  private static $entity = null;
  
  function fetch() {
    $entity = Collection::find($this->name());
    if(is_null($entity)) { throw new k_PageNotFound(); }
    
    return $entity;
  }
  
  function renderJson() {
    $entity = $this->fetch();
    $response = new k_HttpResponse(200);
    $response->setContentType('application/json');
    $response->setContent(json_encode($entity));
    throw $response;
  }
}