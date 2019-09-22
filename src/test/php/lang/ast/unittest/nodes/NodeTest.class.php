<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\Literal;
use lang\ast\nodes\ReturnStatement;
use unittest\TestCase;

abstract class NodeTest extends TestCase {

  protected function returns($literal) {
    return new ReturnStatement(new Literal($literal));
  }
}