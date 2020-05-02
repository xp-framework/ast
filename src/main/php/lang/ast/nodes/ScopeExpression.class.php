<?php namespace lang\ast\nodes;

use lang\IllegalStateException;
use lang\ast\Node;

class ScopeExpression extends Node {
  public $kind= 'scope';
  public $type, $member;

  public function __construct($type, $member, $line= -1) {
    $this->type= $type;
    $this->member= $member;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->member]; }

  public function resolve($scope) {
    $type= $scope->type($this->type);

    // T::$variable, T::class, T::constant vs. T::method()
    if ($this->member instanceof Variable) {
      if ($n= $type->property($this->member->name)) return $n->resolve($scope);
      throw new IllegalStateException('No such property '.$this->type.'::$'.$this->member->name);
    } else if (!($this->member instanceof Literal)) {
      throw new IllegalStateException('Cannot resolve '.$this->type.'::'.$this->member->kind);
    } else if ('class' === $this->member->expression) {
      return ltrim($type->name, '\\');
    } else {
      if ($n= $type->constant($this->member->expression)) return $n->resolve($scope);
      throw new IllegalStateException('No such constant '.$this->type.'::'.$this->member->expression);
    }
  }
}