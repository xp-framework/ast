<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\{CaseLabel, Literal, SwitchStatement, Variable};
use unittest\Test;

class SwitchStatementTest extends NodeTest {
  private $expression;

  /** @return void */
  public function setUp() {
    $this->expression= new Variable('expression');
  }

  #[Test]
  public function can_create() {
    new SwitchStatement($this->expression, []);
  }

  #[Test]
  public function expression() {
    $this->assertEquals($this->expression, (new SwitchStatement($this->expression, []))->expression);
  }

  #[Test]
  public function empty_cases() {
    $this->assertEquals([], (new SwitchStatement($this->expression, []))->cases);
  }

  #[Test]
  public function cases() {
    $cases= [
      new CaseLabel(new Literal(0), [$this->returns('"no"')]),
      new CaseLabel(new Literal(1), [$this->returns('"one"')]),
      new CaseLabel(null, [$this->returns('"more"')])
    ];
    $this->assertEquals($cases, (new SwitchStatement($this->expression, $cases))->cases);
  }

  #[Test]
  public function children() {
    $cases= [
      new CaseLabel(new Literal(0), [$this->returns('"no"')]),
      new CaseLabel(new Literal(1), [$this->returns('"one"')]),
      new CaseLabel(null, [$this->returns('"more"')])
    ];
    $this->assertEquals(
      array_merge([$this->expression], $cases),
      $this->childrenOf(new SwitchStatement($this->expression, $cases))
    );
  }
}