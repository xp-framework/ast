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
   * Returns a type for a given name
   *
   * @param  string $name
   * @return lang.ast.types.Type
   */
  public function type($name) { return $this->scope->type($name); }

  /**
   * Returns all types
   *
   * @return lang.ast.types.Type[]
   */
  public function types() { return $this->scope->types; }
}