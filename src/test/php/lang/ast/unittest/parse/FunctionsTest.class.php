<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{
  ArrayLiteral,
  Assignment,
  BinaryExpression,
  Braced,
  FunctionDeclaration,
  Literal,
  Parameter,
  ReturnStatement,
  Signature,
  Variable,
  YieldExpression,
  YieldFromExpression
};
use lang\ast\types\{IsFunction, IsLiteral, IsNullable, IsUnion, IsValue};
use unittest\{Assert, Test, Values};

class FunctionsTest extends ParseTest {

  /** @return iterable */
  private function types() {
    yield ['string', new IsLiteral('string')];
    yield ['Test', new IsValue('Test')];
    yield ['\unittest\Test', new IsValue('\unittest\Test')];
    yield ['?string', new IsNullable(new IsLiteral('string'))];
    yield ['?(string|int)', new IsNullable(new IsUnion([new IsLiteral('string'), new IsLiteral('int')]))];
    yield ['string|int', new IsUnion([new IsLiteral('string'), new IsLiteral('int')])];
    yield ['string|function(): void', new IsUnion([new IsLiteral('string'), new IsFunction([], new IsLiteral('void'))])];
    yield ['string|(function(): void)', new IsUnion([new IsLiteral('string'), new IsFunction([], new IsLiteral('void'))])];
    yield ['function(): string', new IsFunction([], new IsLiteral('string'))];
    yield ['(function(): string)', new IsFunction([], new IsLiteral('string'))];
  }

  #[Test]
  public function empty_function_without_parameters() {
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [], self::LINE)],
      'function a() { }'
    );
  }

  #[Test]
  public function two_functions() {
    $this->assertParsed(
      [
        new FunctionDeclaration('a', new Signature([], null, self::LINE), [], self::LINE),
        new FunctionDeclaration('b', new Signature([], null, self::LINE), [], self::LINE)
      ],
      'function a() { } function b() { }'
    );
  }

  #[Test, Values(['param', 'protected'])]
  public function with_parameter($name) {
    $params= [new Parameter($name, null, null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a($'.$name.') { }'
    );
  }

  #[Test]
  public function with_reference_parameter() {
    $params= [new Parameter('param', null, null, true, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a(&$param) { }'
    );
  }

  #[Test]
  public function dangling_comma_in_parameter_lists() {
    $params= [new Parameter('param', null, null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a($param, ) { }'
    );
  }

  #[Test, Values('types')]
  public function with_typed_parameter($declaration, $expected) {
    $params= [new Parameter('param', $expected, null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a('.$declaration.' $param) { }'
    );
  }

  #[Test]
  public function with_nullable_typed_parameter() {
    $params= [new Parameter('param', new IsNullable(new IsLiteral('string')), null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a(?string $param) { }'
    );
  }

  #[Test]
  public function with_variadic_parameter() {
    $params= [new Parameter('param', null, null, false, true, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a(... $param) { }'
    );
  }

  #[Test]
  public function with_optional_parameter() {
    $params= [new Parameter('param', null, new Literal('null', self::LINE), false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a($param= null) { }'
    );
  }

  #[Test]
  public function with_parameter_named_function() {
    $params= [new Parameter('function', null, null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a($function, ) { }'
    );
  }

  #[Test]
  public function with_typed_parameter_named_function() {
    $params= [new Parameter('function', new IsFunction([], new IsLiteral('void')), null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null, self::LINE), [], self::LINE)],
      'function a((function(): void) $function) { }'
    );
  }

  #[Test, Values('types')]
  public function with_return_type($declaration, $expected) {
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], $expected, self::LINE), [], self::LINE)],
      'function a(): '.$declaration.' { }'
    );
  }

  #[Test]
  public function generator() {
    $yield= new YieldExpression(null, null, self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      'function a() { yield; }'
    );
  }

  #[Test]
  public function generator_with_value() {
    $yield= new YieldExpression(null, new Literal('1', self::LINE), self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      'function a() { yield 1; }'
    );
  }

  #[Test]
  public function generator_with_key_and_value() {
    $yield= new YieldExpression(new Literal('"number"', self::LINE), new Literal('1', self::LINE), self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      'function a() { yield "number" => 1; }'
    );
  }

  #[Test]
  public function generator_delegation() {
    $yield= new YieldFromExpression(new ArrayLiteral([], self::LINE), self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      'function a() { yield from []; }'
    );
  }

  #[Test]
  public function assign_to_yield() {
    $yield= new Assignment(
      new Variable('value', self::LINE),
      '=',
      new YieldExpression(null, null, self::LINE),
      self::LINE
    );
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      'function a() { $value= yield; }'
    );
  }

  #[Test]
  public function assign_to_yield_with_braced() {
    $yield= new Assignment(
      new Variable('value', self::LINE),
      '=',
      new YieldExpression(null, new Braced(new Literal('1', self::LINE), self::LINE), self::LINE),
      self::LINE
    );
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      'function a() { $value= yield (1); }'
    );
  }


  #[Test]
  public function assign_to_yield_in_braces() {
    $yield= new Assignment(
      new Variable('value', self::LINE),
      '=',
      new Braced(new YieldExpression(null, null, self::LINE), self::LINE),
      self::LINE
    );
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      'function a() { $value= (yield); }'
    );
  }

  #[Test, Values(['function a() { $value= [yield]; }', 'function a() { $value= [yield, ]; }'])]
  public function assign_to_yield_in_array($declaration) {
    $yield= new Assignment(
      new Variable('value', self::LINE),
      '=',
      new ArrayLiteral([[null, new YieldExpression(null, null, self::LINE)]], self::LINE),
      self::LINE
    );
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      $declaration
    );
  }

  #[Test, Values(['function a() { $value= [yield => 1]; }', 'function a() { $value= [yield => 1, ]; }'])]
  public function assign_to_yield_in_map($declaration) {
    $yield= new Assignment(
      new Variable('value', self::LINE),
      '=',
      new ArrayLiteral([[new YieldExpression(null, null, self::LINE), new Literal('1', self::LINE)]], self::LINE),
      self::LINE
    );
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null, self::LINE), [$yield], self::LINE)],
      $declaration
    );
  }
}