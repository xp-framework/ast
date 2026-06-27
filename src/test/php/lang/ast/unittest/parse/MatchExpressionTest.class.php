<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{
  MatchExpression,
  MatchCondition,
  Literal,
  Variable,
  Block,
  ReturnStatement,
  ThrowExpression,
  NewExpression,
  Scalar
};
use lang\ast\types\IsValue;
use test\{Assert, Test};

class MatchExpressionTest extends ParseTest {

  #[Test]
  public function empty_match() {
    $this->assertParsed(
      [new MatchExpression(new Literal('true', self::LINE), [], null, self::LINE)],
      'match (true) { };'
    );
  }

  #[Test]
  public function match() {
    $cases= [
      new MatchCondition([new Scalar('0', 'integer', self::LINE)], new Literal('true', self::LINE), self::LINE),
      new MatchCondition([new Scalar('1', 'integer', self::LINE)], new Literal('false', self::LINE), self::LINE)
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, null, self::LINE)],
      'match ($arg) { 0 => true, 1 => false };'
    );
  }

  #[Test]
  public function match_with_default_block() {
    $default= new Block([new ReturnStatement(new Literal('false', self::LINE), self::LINE)], self::LINE);
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), [], $default, self::LINE)],
      'match ($arg) { default { return false; } };'
    );
  }

  #[Test]
  public function match_with_case_block() {
    $cases= [new MatchCondition(
      [new Scalar('0', 'integer', self::LINE)],
      new Block([new ReturnStatement(new Literal('false', self::LINE), self::LINE)], self::LINE),
      self::LINE
    )];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, null, self::LINE)],
      'match ($arg) { 0 { return false; } };'
    );
  }

  #[Test]
  public function match_with_throw_expression() {
    $default= new ThrowExpression(new NewExpression(new IsValue('\Exception'), [], self::LINE), self::LINE);
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), [], $default, self::LINE)],
      'match ($arg) { default => throw new Exception() };'
    );
  }

  #[Test]
  public function match_without_condition() {
    $this->assertParsed(
      [new MatchExpression(null, [], null, self::LINE)],
      'match { };'
    );
  }

  #[Test]
  public function match_with_trailing_comma() {
    $cases= [
      new MatchCondition([new Scalar('0', 'integer', self::LINE)], new Literal('true', self::LINE), self::LINE),
      new MatchCondition([new Scalar('1', 'integer', self::LINE)], new Literal('false', self::LINE), self::LINE)
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, null, self::LINE)],
      'match ($arg) { 0 => true, 1 => false, };'
    );
  }

  #[Test]
  public function match_with_multiple_cases() {
    $cases= [
      new MatchCondition(
        [new Scalar('0', 'integer', self::LINE), new Scalar('1', 'integer', self::LINE)],
        new Literal('true', self::LINE),
        self::LINE
      ),
      new MatchCondition(
        [new Scalar('2', 'integer', self::LINE), new Scalar('3', 'integer', self::LINE)],
        new Literal('false', self::LINE),
        self::LINE
      )
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, null, self::LINE)],
      'match ($arg) { 0, 1 => true, 2, 3 => false };'
    );
  }

  #[Test]
  public function match_with_default() {
    $cases= [
      new MatchCondition([new Scalar('0', 'integer', self::LINE)], new Literal('true', self::LINE), self::LINE),
      new MatchCondition([new Scalar('1', 'integer', self::LINE)], new Literal('false', self::LINE), self::LINE)
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, new Literal('null', self::LINE), self::LINE)],
      'match ($arg) { 0 => true, 1 => false, default => null };'
    );
  }
}