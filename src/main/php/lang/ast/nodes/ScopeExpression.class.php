<?php namespace lang\ast\nodes;

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
  public function children() {
    return $this->type instanceof parent ? [&$this->type, &$this->member] : [&$this->member];
  }
}