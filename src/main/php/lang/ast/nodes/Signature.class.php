<?php namespace lang\ast\nodes;

use lang\ast\Node;

/** @test lang.ast.unittest.nodes.SignatureTest */
class Signature extends Node {
  public $kind= 'signature';
  public $parameters, $returns, $byref;
  public $generic= null;

  public function __construct($parameters= [], $returns= null, $byref= false, $line= -1) {
    $this->parameters= $parameters;
    $this->returns= $returns;
    $this->byref= $byref;
    $this->line= $line;
  }

  public function add(Parameter $p) {
    $this->parameters[]= $p;
  }

  public function insert($offset, Parameter $p) {
    if (0 === $offset) {
      array_unshift($this->parameters, $p);
    } else {
      array_splice($this->parameters, $offset, 0, [$p]);
    }
  }
}