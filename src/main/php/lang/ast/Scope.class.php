<?php namespace lang\ast;

use lang\ast\types\{Compiled, Reflection};

/**
 * Scope
 *
 * @test  xp://lang.unittest.ast.ScopeTest
 */
class Scope {
  private static $defaults= [
    'string'   => true,
    'int'      => true,
    'float'    => true,
    'double'   => true,
    'bool'     => true,
    'array'    => true,
    'void'     => true,
    'callable' => true,
    'iterable' => true,
    'object'   => true,
    'self'     => true,
    'static'   => true,
    'parent'   => true,
    'mixed'    => true
  ];

  public $parent;
  public $package= null;
  public $annotations= [];
  public $imports= [];
  public $types= [];

  public function __construct(self $parent= null) {
    $this->parent= $parent;
  }

  /**
   * Sets package
   *
   * @param  string $name
   * @return void
   */
  public function package($name) {
    $this->package= null === $name ? '' : '\\'.$name;
  }

  /**
   * Adds an import
   *
   * @param  string $name
   * @param  string $alias
   * @return void
   */
  public function import($name, $alias= null) {
    $this->imports[$alias ?: substr($name, strrpos($name,  '\\') + 1)]= '\\'.$name;
  }

  public function declare($name, $type) {
    $resolved= $this->resolve($name);
    $this->types[$resolved]= new Compiled($type, $this);
    return $type;
  }

  public function type($name) {
    $resolved= $this->resolve($name);
    $t= $this->types[$resolved] ?? null;

    if (null === $t) {
      return $this->parent ? $this->parent->type($name) : (
        class_exists($resolved) || interface_exists($resolved) || trait_exists($resolved) ? new Reflection($resolved) : null
      );
    } else if ($t instanceof Compiled) {
      return $t;
    } else {
      return $this->type($t);
    }
  }

  public function enter($type) {
    $scope= new self($this);
    $scope->types['self']= $type;
    $scope->types['static']= $type;
    $scope->types['parent']= $type->parent ?? null;
    return $scope;
  }

  /**
   * Resolves a type to a fully qualified name
   *
   * @param  string $name
   * @return string
   */
  public function resolve($name) {
    if (null === $name || '' === $name) {
      return '';
    } else if ('\\' === $name[0]) {
      return $name;
    } else if (isset(self::$defaults[$name])) {
      return $name;
    } else if (isset($this->imports[$name])) {
      return $this->imports[$name];
    } else if ($this->package) {
      return $this->package.'\\'.$name;
    } else if ($this->parent) {
      return $this->parent->resolve($name);
    } else {
      return '\\'.$name;
    }
  }
}