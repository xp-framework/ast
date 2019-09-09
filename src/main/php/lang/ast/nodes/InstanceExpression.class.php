<?php namespace lang\ast\nodes;

use lang\ast\Node;

class InstanceExpression extends Node {
  public $kind= 'instance';
  public $expression, $member;

  public function __construct($expression, $member, $line= -1) {
    $this->expression= $expression;
    $this->member= $member;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    return [$this->expression, $this->member];
  }
}