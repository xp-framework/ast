<?php namespace lang\ast\nodes;

class InterfaceDeclaration extends TypeDeclaration {
  public $kind= 'interface';
  public $name, $modifiers, $parents, $body, $annotations, $comment;

  public function __construct($modifiers, $name, $parents, $body, $annotations= [], $comment= null, $line= -1) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->parents= $parents;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
    $this->line= $line;
  }
}