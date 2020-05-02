<?php namespace lang\ast;

use lang\IllegalStateException;

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
   * Resolves this node
   *
   * @param  lang.ast.Scope $scope
   * @return var
   */
  public function resolve($scope) {
    throw new IllegalStateException('Cannot resolve '.$this->kind);
  }
}