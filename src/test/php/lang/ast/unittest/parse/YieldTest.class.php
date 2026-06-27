<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{
  ArrayLiteral,
  BinaryExpression,
  Braced,
  CastExpression,
  InvokeExpression,
  Literal,
  OffsetExpression,
  TernaryExpression,
  UnaryExpression,
  Variable,
  YieldExpression,
  YieldFromExpression
};
use lang\ast\types\IsLiteral;
use test\{Assert, Test, Values};

class YieldTest extends ParseTest {

  /** These should be attached as `(yield [expression])` */
  private function expressions() {
    yield ['true', new Literal('true', self::LINE)];

    // Braces
    yield ['[]', new ArrayLiteral([], self::LINE)];
    yield ['(true)', new Braced(new Literal('true', self::LINE), self::LINE)];
    yield ['(string)$a', new CastExpression(new IsLiteral('string'), new Variable('a', self::LINE), self::LINE)];

    // Prefix operators
    yield ['-$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '-', self::LINE)];
    yield ['+$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '+', self::LINE)];
    yield ['~$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '~', self::LINE)];
    yield ['!$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '!', self::LINE)];
    yield ['@$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '@', self::LINE)];
    yield ['&$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '&', self::LINE)];
    yield ['++$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '++', self::LINE)];
    yield ['--$a', new UnaryExpression('prefix', new Variable('a', self::LINE), '--', self::LINE)];
  }

  #[Test]
  public function plain_yield() {
    $this->assertParsed(
      [new YieldExpression(null, null, self::LINE)],
      'yield;'
    );
  }

  #[Test, Values(from: 'expressions')]
  public function yield_with($literal, $expression) {
    $this->assertParsed(
      [new YieldExpression(null, $expression, self::LINE)],
      'yield '.$literal.';'
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
  public function yield_inside_offset() {
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
  public function yield_inside_argument() {
    $this->assertParsed(
      [new InvokeExpression(
        new Literal('f', self::LINE),
        [new YieldExpression(null, null, self::LINE)],
        self::LINE
      )],
      'f(yield);'
    );
  }

  #[Test]
  public function yield_as_array_element() {
    $this->assertParsed(
      [new ArrayLiteral(
        [[null, new YieldExpression(null, null, self::LINE)], [null, new Literal('true', self::LINE)]],
        self::LINE
      )],
      '[yield, true];'
    );
  }

  #[Test]
  public function yield_as_map_key() {
    $this->assertParsed(
      [new ArrayLiteral(
        [[new YieldExpression(null, null, self::LINE), new Literal('true', self::LINE)]],
        self::LINE
      )],
      '[yield => true];'
    );
  }

  #[Test, Values(['||', '&&', '??', '?:'])]
  public function yield_binary($operator) {
    $this->assertParsed(
      [new BinaryExpression(
        new YieldExpression(null, null, self::LINE),
        $operator,
        new Literal('true', self::LINE),
        self::LINE
      )],
      'yield '.$operator.' true;'
    );
  }

  #[Test]
  public function yield_ternary() {
    $this->assertParsed(
      [new TernaryExpression(
        new YieldExpression(null, null, self::LINE),
        new Literal('true', self::LINE),
        new Literal('false', self::LINE),
        self::LINE
      )],
      'yield ? true : false;'
    );
  }

  #[Test]
  public function yield_offset_coalesce() {
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