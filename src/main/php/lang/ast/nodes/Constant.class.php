<?php namespace lang\ast\nodes;

class Constant extends Value implements Member {
  public $name, $modifiers, $expression;

  public function __construct($modifiers, $name, $expression) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->expression= $expression;
  }

  /** @return string */
  public function kind() { return 'const'; }

  /** @return string */
  public function lookup() { return $this->name; }
}