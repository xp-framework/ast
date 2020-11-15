<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{ArrayLiteral, BinaryExpression, FunctionDeclaration, Literal, Parameter, ReturnStatement, Signature, YieldExpression, YieldFromExpression};
use lang\ast\types\{IsFunction, IsLiteral, IsNullable, IsUnion};
use unittest\{Assert, Test, Values};

class FunctionsTest extends ParseTest {

  #[Test]
  public function empty_function_without_parameters() {
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null), [], self::LINE)],
      'function a() { }'
    );
  }

  #[Test]
  public function two_functions() {
    $this->assertParsed(
      [
        new FunctionDeclaration('a', new Signature([], null), [], self::LINE),
        new FunctionDeclaration('b', new Signature([], null), [], self::LINE)
      ],
      'function a() { } function b() { }'
    );
  }

  #[Test, Values(['param', 'protected'])]
  public function with_parameter($name) {
    $params= [new Parameter($name, null, null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a($'.$name.') { }'
    );
  }

  #[Test]
  public function with_reference_parameter() {
    $params= [new Parameter('param', null, null, true, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a(&$param) { }'
    );
  }

  #[Test]
  public function dangling_comma_in_parameter_lists() {
    $params= [new Parameter('param', null, null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a($param, ) { }'
    );
  }

  #[Test]
  public function with_typed_parameter() {
    $params= [new Parameter('param', new IsLiteral('string'), null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a(string $param) { }'
    );
  }

  #[Test]
  public function with_nullable_typed_parameter() {
    $params= [new Parameter('param', new IsNullable(new IsLiteral('string')), null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a(?string $param) { }'
    );
  }

  #[Test]
  public function with_variadic_parameter() {
    $params= [new Parameter('param', null, null, false, true, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a(... $param) { }'
    );
  }

  #[Test]
  public function with_optional_parameter() {
    $params= [new Parameter('param', null, new Literal('null', self::LINE), false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a($param= null) { }'
    );
  }

  #[Test]
  public function with_parameter_named_function() {
    $params= [new Parameter('function', null, null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a($function, ) { }'
    );
  }

  #[Test]
  public function with_typed_parameter_named_function() {
    $params= [new Parameter('function', new IsFunction([], new IsLiteral('void')), null, false, false, null, [])];
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature($params, null), [], self::LINE)],
      'function a((function(): void) $function) { }'
    );
  }

  #[Test]
  public function with_return_type() {
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], new IsLiteral('void')), [], self::LINE)],
      'function a(): void { }'
    );
  }

  #[Test, Values(['function(): string', '(function(): string)'])]
  public function with_function_return_type($type) {
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], new IsFunction([], new IsLiteral('string'))), [], self::LINE)],
      'function a(): '.$type.' { }'
    );
  }

  #[Test]
  public function with_nullable_return_type() {
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], new IsNullable(new IsLiteral('string'))), [], self::LINE)],
      'function a(): ?string { }'
    );
  }

  #[Test]
  public function with_union_return_type() {
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], new IsUnion([new IsLiteral('string'), new IsLiteral('int')])), [], self::LINE)],
      'function a(): string|int { }'
    );
  }

  #[Test]
  public function generator() {
    $yield= new YieldExpression(null, null, self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null), [$yield], self::LINE)],
      'function a() { yield; }'
    );
  }

  #[Test]
  public function generator_with_value() {
    $yield= new YieldExpression(null, new Literal('1', self::LINE), self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null), [$yield], self::LINE)],
      'function a() { yield 1; }'
    );
  }

  #[Test]
  public function generator_with_key_and_value() {
    $yield= new YieldExpression(new Literal('"number"', self::LINE), new Literal('1', self::LINE), self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null), [$yield], self::LINE)],
      'function a() { yield "number" => 1; }'
    );
  }

  #[Test]
  public function generator_delegation() {
    $yield= new YieldFromExpression(new ArrayLiteral([], self::LINE), self::LINE);
    $this->assertParsed(
      [new FunctionDeclaration('a', new Signature([], null), [$yield], self::LINE)],
      'function a() { yield from []; }'
    );
  }
}