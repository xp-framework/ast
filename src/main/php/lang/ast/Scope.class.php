<?php namespace lang\ast;

/**
 * Scope
 *
 * @test  xp://lang.unittest.ast.ScopeTest
 */
class Scope {
  private static $defaults= [
    'string'   => 'string',
    'int'      => 'int',
    'float'    => 'float',
    'double'   => 'double',
    'bool'     => 'bool',
    'array'    => 'array',
    'void'     => 'void',
    'callable' => 'callable',
    'iterable' => 'iterable',
    'object'   => 'object',
    'self'     => 'self',
    'static'   => 'static',
    'parent'   => 'parent',
    'mixed'    => 'mixed'
  ];

  public $parent, $imports;
  public $package= null;
  public $annotations= [];
  private $types= [];

  public function __construct(self $parent= null) {
    $this->parent= $parent;
    $this->imports= self::$defaults;
  }

  /**
   * Sets package
   *
   * @param  string $name
   * @return void
   */
  public function package($name) {
    $this->package= '\\'.$name;
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
    $this->types[$resolved]= $type;
    return $type;
  }

  public function type($name) {
    $resolved= $this->resolve($name);
    if (isset($this->types[$resolved])) {
      return new types\Compiled($this->types[$resolved], $this);
    }

    // TODO: Locate source?
    // return $this->locate->type($resolved);
    return $this->parent ? $this->parent->type($name) : new types\Reflection($resolved); // FIXME: GlobalScope
  }

  public function enter($type) {
    $scope= new self($this);
    $scope->imports['self']= $type->name;
    $scope->imports['static']= $type->name;
    if (isset($type->parent)) {
      $scope->imports['parent']= $type->parent;
    }
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