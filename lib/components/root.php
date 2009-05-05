<?php
class components_Root extends k_Component {
  function map($name) {
    if ($name == 'collections') {
      return 'components_collections_List';
    }
  }
  
  function dispatch() {
    $t = new k_Template(file_join(dirname(__FILE__),'..','..','templates','document.tpl.php'));
    return
      $t->render(
        $this,
        array(
          'content' => parent::dispatch(),
          'title' => $this->document->title(),
          'scripts' => $this->document->scripts(),
          'styles' => $this->document->styles(),
          'onload' => $this->document->onload()));
  }
  function GET() {
    $t = new k_Template(file_join(dirname(__FILE__),'..','..','templates','root.tpl.php'));
    return $t->render($this);
  }
}
