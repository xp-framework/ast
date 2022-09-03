<?php namespace lang\ast;

use lang\ast\types\{Compiled, Reflection};

/**
 * Scope with package and imports
 *
 * @test  xp://lang.unittest.ast.ScopeTest
 */
class Scope {
  private static $defaults= [
    'string'   => true,
    'int'      => true,
    'float'    => true,
    'bool'     => true,
    'mixed'    => true,
    'array'    => true,
    'void'     => true,
    'never'    => true,
    'resource' => true,
    'callable' => true,
    'iterable' => true,
    'object'   => true,
    'null'     => true,
    'false'    => true,
    'true'     => true,
    'self'     => true,
    'static'   => true,
    'parent'   => true,
    'double'   => true,  // BC
  ];

  public $parent;
  public $package= null;
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
    $this->imports[$alias ?: (false === ($p= strrpos($name,  '\\')) ? $name : substr($name, $p + 1))]= '\\'.$name;
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
    } else if (0 === strncmp($name, 'namespace\\', 10)) {
      return $this->package.'\\'.substr($name, 10);
    } else if ($this->package) {
      return $this->package.'\\'.$name;
    } else if ($this->parent) {
      return $this->parent->resolve($name);
    } else {
      return '\\'.$name;
    }
  }
}