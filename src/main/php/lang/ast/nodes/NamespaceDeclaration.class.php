<?php namespace lang\ast\nodes;

class NamespaceDeclaration extends Value {
  public $kind= 'namespace';
  public $name;

  public function __construct($name, $line= -1) {
    $this->name= $name;
    $this->line= $line;
  }
}