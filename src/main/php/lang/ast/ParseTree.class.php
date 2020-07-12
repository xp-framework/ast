<?php namespace lang\ast;

class ParseTree {
  private $scope, $file;
  private $children= [];

  /**
   * Creates a new parse tree
   *
   * @param  iterable $children
   * @param  ?lang.ast.Scope $scope
   * @param  ?string $file
   */
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

  /**
   * Returns a type by its name
   *
   * @param  string $name
   * @return ?lang.ast.TypeDeclaration
   */
  public function type($name) {
    foreach ($this->children as $node) {
      if ($node->is('@type') && $name === $node->name()) return $node;
    }
    return null;
  }

  /**
   * Returns all types
   *
   * @return iterable
   */
  public function types() {
    foreach ($this->children as $node) {
      if ($node->is('@type')) yield $this->scope->resolve($node->name) => $node;
    }
  }
}