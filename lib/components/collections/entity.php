<?php
require_once 'models/collection.php';

class components_collections_Entity extends k_Component {
  function renderJson() {
    $response = new k_HttpResponse(200);
    $response->setContentType('application/json');
    $response->setContent(json_encode(Collection::find($this->name())));
    throw $response;
  }
}