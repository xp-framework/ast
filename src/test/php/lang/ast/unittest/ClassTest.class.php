<?php namespace lang\ast\unittest;

use lang\ast\nodes\{ClassDeclaration, Method, Signature};
use unittest\{Assert, Before, Test};

class ClassTest {
  private $method;

  #[Before]
  public function initialize() {
    $this->method= new Method([], 'toString', new Signature([], null), [], [], null);
  }

  #[Test]
  public function method() {
    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);

    Assert::equals($this->method, $fixture->method('toString'));
  }

  #[Test]
  public function methods() {
    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);
    
    Assert::equals(['toString' => $this->method], iterator_to_array($fixture->methods()));
  }

  #[Test]
  public function overwrite() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], null, 'Overwritten');

    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);
    $fixture->overwrite($overwritten);

    Assert::equals($overwritten, $fixture->method('toString'));
  }

  #[Test]
  public function declare() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], null, 'Overwritten');

    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);
    $fixture->declare($overwritten);

    Assert::equals($this->method, $fixture->method('toString'));
  }
}