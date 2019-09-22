<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\CaseLabel;
use lang\ast\nodes\Literal;
use lang\ast\nodes\SwitchStatement;
use lang\ast\nodes\Variable;

class SwitchStatementTest extends NodeTest {
  private $expression;

  /** @return void */
  public function setUp() {
    $this->expression= new Variable('expression');
  }

  #[@test]
  public function can_create() {
    new SwitchStatement($this->expression, []);
  }

  #[@test]
  public function expression() {
    $this->assertEquals($this->expression, (new SwitchStatement($this->expression, []))->expression);
  }

  #[@test]
  public function empty_cases() {
    $this->assertEquals([], (new SwitchStatement($this->expression, []))->cases);
  }

  #[@test]
  public function cases() {
    $cases= [
      new CaseLabel(new Literal(0), [$this->returns('"no"')]),
      new CaseLabel(new Literal(1), [$this->returns('"one"')]),
      new CaseLabel(null, [$this->returns('"more"')])
    ];
    $this->assertEquals($cases, (new SwitchStatement($this->expression, $cases))->cases);
  }

  #[@test]
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