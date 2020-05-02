<?php namespace lang\ast\unittest;

use lang\ast\nodes\{ClassDeclaration, Method, Signature};
use unittest\TestCase;

class ClassTest extends TestCase {
  private $method;

  /** @return void */
  public function setUp() {
    $this->method= new Method([], 'toString', new Signature([], null), [], [], null);
  }

  #[@test]
  public function method() {
    $fixture= new ClassDeclaration([], 'Test', null, [], ['toString()' => $this->method], [], null);
    $this->assertEquals($this->method, $fixture->method('toString'));
  }

  #[@test]
  public function methods() {
    $fixture= new ClassDeclaration([], 'Test', null, [], ['toString()' => $this->method], [], null);
    $this->assertEquals(['toString' => $this->method], iterator_to_array($fixture->methods()));
  }

  #[@test]
  public function overwrite() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], [], 'Overwritten');

    $fixture= new ClassDeclaration([], 'Test', null, [], ['toString()' => $this->method], [], null);
    $fixture->overwrite($overwritten);
    $this->assertEquals($overwritten, $fixture->method('toString'));
  }

  #[@test]
  public function inject() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], [], 'Overwritten');

    $fixture= new ClassDeclaration([], 'Test', null, [], ['toString()' => $this->method], [], null);
    $fixture->inject($overwritten);
    $this->assertEquals($this->method, $fixture->method('toString'));
  }
}