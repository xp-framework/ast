<?php namespace lang\ast;

abstract class Node {
  public $line= -1;
  public $kind= null;

  /** @return iterable */
  public function children() { return []; }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return $this->kind === $kind;
  }
}