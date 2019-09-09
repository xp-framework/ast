<?php namespace lang\ast;

abstract class Node {
  public $line= -1;
  public $kind= null;

  /** @return iterable */
  public function children() { return []; }
}