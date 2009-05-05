<?php
require_once file_join(dirname(__FILE__),'..','..','json.php');

class components_collections_List extends k_Component {
  function renderHtml() {
    global $collections;
    $this->document->setTitle("Collections");
    $t = new k_Template("templates/collections/list.tpl.php");
    return $t->render(
      $this,
      array(
        'collections' => $collections->all()));
  }
  function renderJson() {
    global $collections;
    $json = new Services_JSON();
    return $json->encode($collections->all());
  }
}