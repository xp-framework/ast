<?php namespace lang\ast\nodes;

class InterfaceType extends ValueType {
  public $name, $modifiers, $parents, $body, $annotations, $comment;

  public function __construct($modifiers, $name, $parents, $body, $annotations= [], $comment= null) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->parents= $parents;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
  }
}