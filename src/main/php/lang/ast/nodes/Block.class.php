<?php namespace lang\ast\nodes;

use Traversable, IteratorAggregate;
use lang\ast\Node;

class Block extends Node implements IteratorAggregate {
  public $kind= 'block';
  public $statements;

  public function __construct($statements, $line= -1) {
    $this->statements= $statements;
    $this->line= $line;
  }

  /** @return iterable */
  public function children() { return $this->statements; }

  /** Iteration support */
  public function getIterator(): Traversable { yield from $this->statements; }
}