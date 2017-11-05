<?php namespace lang\ast\unittest;

use lang\ast\nodes\ClassValue;
use lang\ast\nodes\Method;
use lang\ast\nodes\Signature;
use lang\ast\Node;

class ClassTest extends \unittest\TestCase {
  private $method;

  /** @return void */
  public function setUp() {
    $this->method= new Method([], 'toString', new Signature([], null), [], [], null);
  }

  #[@test]
  public function method() {
    $fixture= new ClassValue('Test', [], null, [], ['toString()' => new Node(null, 'method', $this->method)], [], null);
    $this->assertEquals($this->method, $fixture->method('toString'));
  }

  #[@test]
  public function methods() {
    $fixture= new ClassValue('Test', [], null, [], ['toString()' => new Node(null, 'method', $this->method)], [], null);
    $this->assertEquals([$this->method], iterator_to_array($fixture->methods()));
  }

  #[@test]
  public function overwrite() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], [], 'Overwritten');

    $fixture= new ClassValue('Test', [], null, [], ['toString()' => new Node(null, 'method', $this->method)], [], null);
    $fixture->overwrite($overwritten);
    $this->assertEquals($overwritten, $fixture->method('toString'));
  }

  #[@test]
  public function inject() {
    $overwritten= new Method([], 'toString', new Signature([], null), [], [], 'Overwritten');

    $fixture= new ClassValue('Test', [], null, [], ['toString()' => new Node(null, 'method', $this->method)], [], null);
    $fixture->inject($overwritten);
    $this->assertEquals($this->method, $fixture->method('toString'));
  }
}