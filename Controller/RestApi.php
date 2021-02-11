<?php 

namespace SchemaSync\Controller;

class RestApi extends \LimeExtra\Controller {
  public function list() {
    return $this->module('schemasync')->list();
  }

  public function sync() {
    return $this->module('schemasync')->syncSchemas();
  }
}
