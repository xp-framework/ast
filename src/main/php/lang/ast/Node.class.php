<?php namespace lang\ast;

use lang\IllegalStateException;

/** Base class for all classes in the `lang.ast.nodes` package */
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

  /**
   * Visits this node
   *
   * @param  lang.ast.Visitor $visitor
   * @return var
   */
  public function visit($visitor) { return $visitor->{$this->kind}($this); }

}