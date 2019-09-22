<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\ArrayLiteral;
use lang\ast\nodes\Literal;

class ArrayLiteralTest extends NodeTest {

  #[@test]
  public function can_create() {
    new ArrayLiteral([]);
  }

  #[@test]
  public function empty_values() {
    $this->assertEquals([], (new ArrayLiteral([]))->values);
  }

  #[@test]
  public function array_values() {
    $values= [[null, new Literal(1)]];
    $this->assertEquals($values, (new ArrayLiteral($values))->values);
  }

  #[@test]
  public function map_values() {
    $values= [[new Literal('"one"'), new Literal(1)]];
    $this->assertEquals($values, (new ArrayLiteral($values))->values);
  }

  #[@test]
  public function array_children() {
    $values= [[null, new Literal(1)]];
    $this->assertEquals([new Literal(1)], $this->childrenOf(new ArrayLiteral($values)));
  }

  #[@test]
  public function map_children() {
    $values= [[new Literal('"one"'), new Literal(1)]];
    $this->assertEquals([new Literal('"one"'), new Literal(1)], $this->childrenOf(new ArrayLiteral($values)));
  }
}