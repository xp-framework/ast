<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{MatchExpression, MatchCondition, Literal, Variable, Block, ReturnStatement, ThrowExpression, NewExpression};
use unittest\{Assert, Test};

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
      new MatchCondition([new Literal('0', self::LINE)], new Literal('true', self::LINE)),
      new MatchCondition([new Literal('1', self::LINE)], new Literal('false', self::LINE))
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, null, self::LINE)],
      'match ($arg) { 0 => true, 1 => false };'
    );
  }

  #[Test]
  public function match_with_block() {
    $default= new Block([new ReturnStatement(new Literal('false', self::LINE), self::LINE)], self::LINE);
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), [], $default, self::LINE)],
      'match ($arg) { default => { return false; } };'
    );
  }

  #[Test]
  public function match_with_throw_expression() {
    $default= new ThrowExpression(new NewExpression('\Exception', [], self::LINE), self::LINE);
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
      new MatchCondition([new Literal('0', self::LINE)], new Literal('true', self::LINE)),
      new MatchCondition([new Literal('1', self::LINE)], new Literal('false', self::LINE))
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, null, self::LINE)],
      'match ($arg) { 0 => true, 1 => false, };'
    );
  }

  #[Test]
  public function match_with_multiple_cases() {
    $cases= [
      new MatchCondition([new Literal('0', self::LINE), new Literal('1', self::LINE)], new Literal('true', self::LINE)),
      new MatchCondition([new Literal('2', self::LINE), new Literal('3', self::LINE)], new Literal('false', self::LINE))
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, null, self::LINE)],
      'match ($arg) { 0, 1 => true, 2, 3 => false };'
    );
  }

  #[Test]
  public function match_with_default() {
    $cases= [
      new MatchCondition([new Literal('0', self::LINE)], new Literal('true', self::LINE)),
      new MatchCondition([new Literal('1', self::LINE)], new Literal('false', self::LINE))
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('arg', self::LINE), $cases, new Literal('null', self::LINE), self::LINE)],
      'match ($arg) { 0 => true, 1 => false, default => null };'
    );
  }
}