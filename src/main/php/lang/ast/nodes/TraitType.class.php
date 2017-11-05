<?php namespace lang\ast\nodes;

class TraitType extends ValueType {
  public $name, $modifiers, $body, $annotations, $comment;

  public function __construct($modifiers, $name, $body, $annotations= [], $comment= null) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
  }
}