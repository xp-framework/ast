<?php namespace lang\ast\nodes;

use lang\ast\Node;

class NamespaceDeclaration extends Node {
  public $kind= 'namespace';
  public $name;

  public function __construct($name, $line= -1) {
    $this->name= $name;
    $this->line= $line;
  }
}