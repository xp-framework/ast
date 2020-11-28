<?php namespace lang\ast\unittest\parse;

use lang\ast\Errors;
use lang\ast\nodes\{CaseLabel, IfStatement, InvokeExpression, Literal, MatchCondition, MatchExpression, SwitchStatement, Variable};
use unittest\{Assert, Before, Test};

class ConditionalTest extends ParseTest {
  private $blocks;

  #[Before]
  public function blocks() {
    $this->blocks= [
      1 => [new InvokeExpression(new Literal('action1', self::LINE), [], self::LINE)],
      2 => [new InvokeExpression(new Literal('action2', self::LINE), [], self::LINE)]
    ];
  }

  #[Test]
  public function plain_if() {
    $this->assertParsed(
      [new IfStatement(new Variable('condition', self::LINE), $this->blocks[1], null, self::LINE)],
      'if ($condition) { action1(); }'
    );
  }

  #[Test]
  public function if_with_else() {
    $this->assertParsed(
      [new IfStatement(new Variable('condition', self::LINE), $this->blocks[1], $this->blocks[2], self::LINE)],
      'if ($condition) { action1(); } else { action2(); }'
    );
  }

  #[Test]
  public function shortcut_if() {
    $this->assertParsed(
      [new IfStatement(new Variable('condition', self::LINE), $this->blocks[1], null, self::LINE)],
      'if ($condition) action1();'
    );
  }

  #[Test]
  public function shortcut_if_else() {
    $this->assertParsed(
      [new IfStatement(new Variable('condition', self::LINE), $this->blocks[1], $this->blocks[2], self::LINE)],
      'if ($condition) action1(); else action2();'
    );
  }

  #[Test]
  public function empty_switch() {
    $this->assertParsed(
      [new SwitchStatement(new Variable('condition', self::LINE), [], self::LINE)],
      'switch ($condition) { }'
    );
  }

  #[Test]
  public function switch_with_one_case() {
    $cases= [new CaseLabel(new Literal('1', self::LINE), $this->blocks[1], self::LINE)];
    $this->assertParsed(
      [new SwitchStatement(new Variable('condition', self::LINE), $cases, self::LINE)],
      'switch ($condition) { case 1: action1(); }'
    );
  }

  #[Test]
  public function switch_with_two_cases() {
    $cases= [
      new CaseLabel(new Literal('1', self::LINE), $this->blocks[1], self::LINE),
      new CaseLabel(new Literal('2', self::LINE), $this->blocks[2], self::LINE)
    ];
    $this->assertParsed(
      [new SwitchStatement(new Variable('condition', self::LINE), $cases, self::LINE)],
      'switch ($condition) { case 1: action1(); case 2: action2(); }'
    );
  }

  #[Test]
  public function switch_with_default() {
    $cases= [new CaseLabel(null, $this->blocks[1], self::LINE)];
    $this->assertParsed(
      [new SwitchStatement(new Variable('condition', self::LINE), $cases, self::LINE)],
      'switch ($condition) { default: action1(); }'
    );
  }

  #[Test]
  public function empty_match() {
    $this->assertParsed(
      [new MatchExpression(new Variable('condition', self::LINE), [], null, self::LINE)],
      'match ($condition) { };'
    );
  }

  #[Test]
  public function match_with_trailing_comma() {
    $cases= [
      new MatchCondition([new Literal('1', self::LINE)], $this->blocks[1][0], self::LINE),
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('condition', self::LINE), $cases, null, self::LINE)],
      'match ($condition) { 1 => action1(), };'
    );
  }

  #[Test]
  public function match_with_two_cases() {
    $cases= [
      new MatchCondition([new Literal('1', self::LINE)], $this->blocks[1][0], self::LINE),
      new MatchCondition([new Literal('2', self::LINE)], $this->blocks[2][0], self::LINE)
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('condition', self::LINE), $cases, null, self::LINE)],
      'match ($condition) { 1 => action1(), 2 => action2() };'
    );
  }

  #[Test]
  public function match_with_multi_expression_case_and_default() {
    $cases= [
      new MatchCondition([new Literal('1', self::LINE), new Literal('2', self::LINE)], $this->blocks[1][0], self::LINE),
    ];
    $this->assertParsed(
      [new MatchExpression(new Variable('condition', self::LINE), $cases, $this->blocks[2][0], self::LINE)],
      'match ($condition) { 1, 2 => action1(), default => action2() };'
    );
  }

  #[Test, Expect(class: Errors::class, withMessage: 'Unexpected (end)')]
  public function unclosed_statement_list() {
    $this->parse('if ($condition) { action1();')->stream()->current();
  }
}