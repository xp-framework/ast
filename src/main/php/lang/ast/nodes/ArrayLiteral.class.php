<?php namespace lang\ast\nodes;

use lang\ast\Node;

/**
 * Array literal - used for both zero-based lists and maps.
 *
 * @test  lang.ast.unittest.nodes.ArrayLiteralTest
 */
class ArrayLiteral extends Node {
  public $kind= 'array';
  public $values;

  public function __construct($values= [], $line= -1) {
    $this->values= $values;
    $this->line= $line;
  }

  /** Appends a given key value pair to a map */
  public function add(Node $key, Node $value): self {
    $this->values[]= [$key, $value];
    return $this;
  }

  /** Appends a given node to a zero-based list */
  public function append(Node $value): self {
    $this->values[]= [null, $value];
    return $this;
  }

  /** @return iterable */
  public function children() {
    foreach ($this->values as $pair) {
      if (null !== $pair[0]) yield $pair[0];
      yield $pair[1];
    }
  }
}