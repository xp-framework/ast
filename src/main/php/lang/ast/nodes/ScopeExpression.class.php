<?php namespace lang\ast\nodes;

class ScopeExpression extends Value {
  public $kind= 'scope';
  public $type, $member;

  public function __construct($type, $member, $line= -1) {
    $this->type= $type;
    $this->member= $member;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return [$this->member]; }
}