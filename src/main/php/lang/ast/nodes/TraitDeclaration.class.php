<?php namespace lang\ast\nodes;

class TraitDeclaration extends TypeDeclaration {
  public $kind= 'trait';

  public function __construct($modifiers, $name, $body, $annotations= [], $comment= null, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
    $this->line= $line;
  }
}