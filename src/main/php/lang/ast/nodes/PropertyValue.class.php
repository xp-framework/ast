<?php namespace lang\ast\nodes;

class PropertyValue extends Annotated implements Member {
  public $name, $modifiers, $expression, $type, $annotations, $comment;

  public function __construct($name, $modifiers, $expression, $type, $annotations, $comment) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->expression= $expression;
    $this->type= $type;
    $this->annotations= $annotations;
    $this->comment= $comment;
  }

  /** @return string */
  public function kind() { return 'property'; }

  /** @return string */
  public function lookup() { return '$'.$this->name; }
}