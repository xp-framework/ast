<?php namespace lang\ast\nodes;

class Signature extends Value {
  public $parameters, $returns;

  public function __construct($parameters= [], $returns= null) {
    $this->parameters= $parameters;
    $this->returns= $returns;
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