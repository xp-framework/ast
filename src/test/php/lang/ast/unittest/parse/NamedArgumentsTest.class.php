<?php namespace lang\ast\unittest\parse;

use lang\ast\nodes\{InstanceExpression, InvokeExpression, Literal, Variable};
use unittest\{Assert, Test};

/**
 * Named argumwents
 *
 * @see  https://wiki.php.net/rfc/named_params
 */
class NamedArgumentsTest extends ParseTest {

  /**
   * Assertion helper
   * 
   * @param  string[] $names
   * @param  array $arguments
   * @throws unittest.AssertionFailedError
   */
  private function assertNamed($names, $arguments) {
    Assert::equals($names, array_keys($arguments));
  }

  #[Test]
  public function one_named() {
    $node= $this->parse('func(named: 1);')->tree()->children()[0];
    $this->assertNamed(['named'], $node->arguments);
  }

  #[Test]
  public function keyword_used_as_name() {
    $node= $this->parse('func(class: T::class);')->tree()->children()[0];
    $this->assertNamed(['class'], $node->arguments);
  }

  #[Test]
  public function named_and_positional() {
    $node= $this->parse('func(1, 2, a: 3, b: 4);')->tree()->children()[0];
    $this->assertNamed([0, 1, 'a', 'b'], $node->arguments);
  }

  #[Test]
  public function function_call() {
    $node= $this->parse('func(1, named: 2);')->tree()->children()[0];
    $this->assertNamed([0, 'named'], $node->arguments);
  }

  #[Test]
  public function instance_method_call() {
    $node= $this->parse('$this->func(1, named: 2);')->tree()->children()[0];
    $this->assertNamed([0, 'named'], $node->arguments);
  }

  #[Test]
  public function class_method_call() {
    $node= $this->parse('self::func(1, named: 2);')->tree()->children()[0];
    $this->assertNamed([0, 'named'], $node->member->arguments);
  }

  #[Test]
  public function new_operator() {
    $node= $this->parse('new T(1, named: 2);')->tree()->children()[0];
    $this->assertNamed([0, 'named'], $node->arguments);
  }

  #[Test]
  public function attributes() {
    $node= $this->parse('#[Values(using: "$values")] class T { }')->tree()->children()[0];
    $this->assertNamed(['using'], $node->annotations['Values']);
  }
}