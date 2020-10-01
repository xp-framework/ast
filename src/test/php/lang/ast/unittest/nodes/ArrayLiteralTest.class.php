<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\{ArrayLiteral, Literal};
use unittest\Test;

class ArrayLiteralTest extends NodeTest {

  #[Test]
  public function can_create() {
    new ArrayLiteral([]);
  }

  #[Test]
  public function empty_values() {
    $this->assertEquals([], (new ArrayLiteral([]))->values);
  }

  #[Test]
  public function array_values() {
    $values= [[null, new Literal(1)]];
    $this->assertEquals($values, (new ArrayLiteral($values))->values);
  }

  #[Test]
  public function map_values() {
    $values= [[new Literal('"one"'), new Literal(1)]];
    $this->assertEquals($values, (new ArrayLiteral($values))->values);
  }

  #[Test]
  public function array_children() {
    $values= [[null, new Literal(1)]];
    $this->assertEquals([new Literal(1)], $this->childrenOf(new ArrayLiteral($values)));
  }

  #[Test]
  public function map_children() {
    $values= [[new Literal('"one"'), new Literal(1)]];
    $this->assertEquals([new Literal('"one"'), new Literal(1)], $this->childrenOf(new ArrayLiteral($values)));
  }
}