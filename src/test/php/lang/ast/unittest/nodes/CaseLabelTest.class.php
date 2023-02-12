<?php namespace lang\ast\unittest\nodes;

use lang\ast\nodes\{CaseLabel, Literal};
use test\{Assert, Before, Test};

class CaseLabelTest extends NodeTest {
  private $expression;

  #[Before]
  public function newExpression() {
    $this->expression= new Literal(0);
  }

  #[Test]
  public function can_create() {
    new CaseLabel($this->expression, []);
  }

  #[Test]
  public function normal_label_has_expression() {
    Assert::equals($this->expression, (new CaseLabel($this->expression, []))->expression);
  }

  #[Test]
  public function default_has_no_expression() {
    Assert::null((new CaseLabel(null, []))->expression);
  }

  #[Test]
  public function normal_label_children() {
    $body= [$this->returns('"no"')];
    Assert::equals(
      array_merge([$this->expression], $body),
      $this->childrenOf(new CaseLabel($this->expression, $body))
    );
  }

  #[Test]
  public function default_children() {
    $body= [$this->returns('"no"')];
    Assert::equals(
      $body,
      $this->childrenOf(new CaseLabel(null, $body))
    );
  }
}