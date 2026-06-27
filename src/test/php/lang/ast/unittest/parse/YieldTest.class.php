<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{YieldExpression, YieldFromExpression, OffsetExpression, Variable, Literal, ArrayLiteral, BinaryExpression};
use test\{Assert, Test};

class YieldTest extends ParseTest {

  #[Test]
  public function plain_yield() {
    $this->assertParsed(
      [new YieldExpression(null, null, self::LINE)],
      'yield;'
    );
  }

  #[Test]
  public function yield_with_value() {
    $this->assertParsed(
      [new YieldExpression(null, new Literal('true', self::LINE), self::LINE)],
      'yield true;'
    );
  }

  #[Test]
  public function yield_array() {
    $this->assertParsed(
      [new YieldExpression(null, new ArrayLiteral([], self::LINE), self::LINE)],
      'yield [];'
    );
  }

  #[Test]
  public function yield_with_pair() {
    $this->assertParsed(
      [new YieldExpression(new Literal('"test"', self::LINE), new Literal('true', self::LINE), self::LINE)],
      'yield "test" => true;'
    );
  }

  #[Test]
  public function yield_from() {
    $this->assertParsed(
      [new YieldFromExpression(new ArrayLiteral([], self::LINE), self::LINE)],
      'yield from [];'
    );
  }

  #[Test]
  public function yield_inside_array() {
    $this->assertParsed(
      [new OffsetExpression(
        new Variable('array', self::LINE),
        new YieldExpression(null, null, self::LINE),
        self::LINE
      )],
      '$array[yield];'
    );
  }

  #[Test]
  public function yield_coalesce() {
    $this->assertParsed(
      [new BinaryExpression(
        new OffsetExpression(
          new Variable('array', self::LINE),
          new YieldExpression(null, null, self::LINE),
          self::LINE
        ),
        '??',
        new Literal('null', self::LINE),
        self::LINE
      )],
      '$array[yield] ?? null;'
    );
  }
}