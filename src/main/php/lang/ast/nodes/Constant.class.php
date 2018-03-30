<?php namespace lang\ast\nodes;

class Constant extends Value implements Member {
  public $name, $modifiers, $expression, $type;

  public function __construct($modifiers, $name, $type, $expression) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->type= $type;
    $this->expression= $expression;
  }

  /** @return string */
  public function kind() { return 'const'; }

  /** @return string */
  public function lookup() { return $this->name; }
}