<?php namespace lang\ast\nodes;

use lang\ast\Node;

class Signature extends Node {
  public $kind= 'signature';
  public $parameters, $returns, $generic;

  public function __construct($parameters= [], $returns= null, $generic= null, $line= -1) {
    $this->parameters= $parameters;
    $this->returns= $returns;
    $this->generic= $generic;
    $this->line= $line;
  }

  public function add(Parameter $p) {
    $this->parameters[]= $p;
  }

  public function insert($offset, Parameter $p) {
    if (0 === $offset) {
      array_unshift($this->parameters, $p);
    } else {
      $this->parameters[]= array_merge(
        array_slice($this->parameters, 0, $p),
        [$p],
        array_slice($this->parameters, $p)
      );
    }
  }
}