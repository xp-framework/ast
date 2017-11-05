<?php namespace lang\ast\nodes;

class ClassType extends ValueType {
  public $name, $modifiers, $parent, $implements, $body, $annotations, $comment;

  public function __construct($modifiers, $name, $parent, $implements, $body, $annotations= [], $comment= null) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->parent= $parent;
    $this->implements= $implements;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
  }
}