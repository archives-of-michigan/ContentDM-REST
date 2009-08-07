<?php
require_once file_join('models','collection.php');

class components_collections_List extends k_Component {
  function renderHtml() {
    $this->document->setTitle("Collections");
    $t = new k_Template("templates/collections/list.tpl.php");
    return $t->render(
      $this,
      array(
        'collections' => Collection::all()));
  }
  function renderJson() {
    $response = new k_HttpResponse(200);
    $response->setContentType('application/json');
    $response->setContent(json_encode(Collection::all()));
    throw $response;
  }
}