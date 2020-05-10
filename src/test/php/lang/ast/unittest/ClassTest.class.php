<?php namespace lang\ast\unittest;

use lang\ast\nodes\{ClassDeclaration, Method, Signature};
use unittest\Assert;

class ClassTest {
  private $method;

  #[@before]
  public function initialize() {
    $this->method= new Method([], 'toString', new Signature([], null), [], [], null);
  }

  #[@test]
  public function method() {
    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);

    Assert::equals($this->method, $fixture->method('toString'));
  }

  #[@test]
  public function methods() {
    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);
    
    Assert::equals(['toString' => $this->method], iterator_to_array($fixture->methods()));
  }

  #[@test]
  public function overwrite() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], [], 'Overwritten');

    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);
    $fixture->overwrite($overwritten);

    Assert::equals($overwritten, $fixture->method('toString'));
  }

  #[@test]
  public function declare() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], [], 'Overwritten');

    $fixture= new ClassDeclaration([], 'Test', null, [], [], null);
    $fixture->declare($this->method);
    $fixture->declare($overwritten);

    Assert::equals($this->method, $fixture->method('toString'));
  }
}