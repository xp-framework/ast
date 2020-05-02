<?php namespace lang\ast;

class ParseTree {
  private $scope, $file;
  private $children= [];

  public function __construct($children, $scope= null, $file= null) {
    foreach ($children as $node) {
      $this->children[]= $node;
    }
    $this->scope= $scope;
    $this->file= $file;
  }

  /** @return lang.ast.Node[] */
  public function children() { return $this->children; }

  /** @return lang.ast.Scope */
  public function scope() { return $this->scope; }

  /** @return string */
  public function file() { return $this->file; }

}