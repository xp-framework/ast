<?php namespace lang\ast;

class Resolveable extends Node {
  public $kind= '$resolveable';
  private $function;

  public function __construct($function) { $this->function= $function; }

  /**
   * Resolves this node
   *
   * @param  lang.ast.Scope $scope
   * @return var
   */
  public function resolve($scope) {
    $f= $this->function;
    return $f($scope);
  }
}