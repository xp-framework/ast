<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Constant extends Node implements Member {
  public $kind= 'const';
  public $holder;
  public $name, $modifiers, $expression, $type;

  public function __construct($modifiers, $name, $type, $expression, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->type= $type;
    $this->expression= $expression;
    $this->line= $line;
  }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return $this->kind === $kind || '@member' === $kind;
  }

  /** @return string */
  public function lookup() { return $this->name; }

  public function resolve($scope) {
    return $this->expression->resolve($scope->enter($scope->type($this->holder)));
  }
}