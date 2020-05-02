<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Array literal - used for both zero-based lists and maps.
 *
 * @test  xp://lang.ast.unittest.nodes.ArrayLiteralTest
 */
class ArrayLiteral extends Node {
  public $kind= 'array';
  public $values;

  public function __construct($values, $line= -1) {
    $this->values= $values;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->values as $pair) {
      if (null !== $pair[0]) yield $pair[0];
      yield $pair[1];
    }
  }

  /** @return var */
  public function resolve($scope) {
    $r= [];
    foreach ($this->values as $pair) {
      if (null === $pair[0]) {
        $r[]= $pair[1]->resolve($scope);
      } else {
        $r[$pair[0]->resolve($scope)]= $pair[1]->resolve($scope);
      }
    }
    return $r;
  }
}