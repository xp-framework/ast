<?php namespace lang\ast\nodes;

use lang\ast\Element;

abstract class Value implements Element {
  public $line= -1;

  /** @return iterable */
  public function children() { return []; }
}