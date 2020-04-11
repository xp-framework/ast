<?php namespace lang\ast\nodes;

class ClassDeclaration extends TypeDeclaration {
  public $kind= 'class';
  public $name, $modifiers, $parent, $implements, $body, $annotations, $comment;

  public function __construct($modifiers, $name, $parent, $implements, $body, $annotations= [], $comment= null, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->parent= $parent;
    $this->implements= $implements;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
    $this->line= $line;
  }

  public function parent() { return $this->parent; }

  public function interfaces() { return $this->implements; }
}