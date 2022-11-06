<?php namespace lang\ast;

use lang\Value;
use util\Objects;

/** The result of a Parse::tree() invocations */
class ParseTree implements Value {
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
      if ($node->is('@type')) yield $this->scope->resolve($node->name->literal()) => $node;
    }
  }

  /** @return string */
  public function hashCode() {
    return Objects::hashOf([$this->scope, $this->file, $this->children]);
  }

  /** @return string */
  public function toString() {
    return nameof($this)."(source: ".$this->file.")@{\n".
      "  scope => ".Objects::stringOf($this->scope, '  ')."\n".
      "  children => ".Objects::stringOf($this->children, '  ')."\n".
    "}";
  }

  /**
   * Compare this parse tree to another
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self
      ? Objects::compare([$this->scope, $this->file, $this->children], [$value->scope, $value->file, $value->children])
      : 1
    ;
  }
}