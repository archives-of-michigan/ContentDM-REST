<?php
class components_Root extends k_Component {
  function map($name) {
    if ($name == 'collections') {
      return 'components_collections_List';
    }
  }
  
  function dispatch() {
    return parent::dispatch();
  }
  function GET() {
    $t = new k_Template(dirname(__FILE__).'/../../templates/root.tpl.php');
    return $t->render($this);
  }
}