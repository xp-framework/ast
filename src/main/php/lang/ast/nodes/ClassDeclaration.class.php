<?php namespace lang\ast\nodes;

class ClassDeclaration extends TypeDeclaration {
  public $kind= 'class';
  public $parent, $implements;

  public function __construct($modifiers, $name, $parent= null, $implements= [], $annotations= [], $comment= null, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->parent= $parent;
    $this->implements= $implements;
    $this->annotations= $annotations;
    $this->comment= $comment;
    $this->line= $line;
  }

  public function parent() { return $this->parent; }

  public function interfaces() { return $this->implements; }
}