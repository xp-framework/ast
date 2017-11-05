<?php namespace lang\ast\nodes;

class Property extends Annotated implements Member {
  public $name, $modifiers, $expression, $type, $annotations, $comment;

  public function __construct($modifiers, $name, $type, $expression= null, $annotations= [], $comment= null) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->type= $type;
    $this->expression= $expression;
    $this->annotations= $annotations;
    $this->comment= $comment;
  }

  /** @return string */
  public function kind() { return 'property'; }

  /** @return string */
  public function lookup() { return '$'.$this->name; }
}