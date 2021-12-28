<?php namespace lang\ast\nodes;

class InterfaceDeclaration extends TypeDeclaration {
  public $kind= 'interface';
  public $parents;

  public function __construct(
    $modifiers,
    $name,
    $parents,
    $body= [],
    $annotations= null,
    $comment= null,
    $line= -1
  ) {
    parent::__construct($modifiers, $name, $body, $annotations, $comment, $line);
    $this->parents= $parents;
  }

  public function parent() { return null; }

  public function interfaces() { return $this->parents; }
}