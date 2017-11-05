<?php namespace lang\ast\nodes;

class ConstValue extends Value implements Member {
  public $name, $modifiers, $expression;

  public function __construct($name, $modifiers, $expression) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->expression= $expression;
  }

  /** @return string */
  public function kind() { return 'const'; }

  /** @return string */
  public function lookup() { return $this->name; }
}