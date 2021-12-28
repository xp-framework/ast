<?php namespace lang\ast\nodes;

class ClassDeclaration extends TypeDeclaration {
  public $kind= 'class';
  public $parent, $implements;

  public function __construct(
    $modifiers,
    $name,
    $parent= null,
    $implements= [],
    $body= [],
    $annotations= null,
    $comment= null,
    $line= -1
  ) {
    parent::__construct($modifiers, $name, $body, $annotations, $comment, $line);
    $this->parent= $parent;
    $this->implements= $implements;
  }

  public function parent() { return $this->parent; }

  public function interfaces() { return $this->implements; }
}