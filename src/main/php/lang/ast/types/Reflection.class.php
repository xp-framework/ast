<?php namespace lang\ast\types;

use lang\IllegalStateException;
use lang\ast\Resolveable;
use lang\ast\nodes\{Property, Constant};

class Reflection {
  private $backing;

  public function __construct($name) {
    $this->backing= new \ReflectionClass($name);

    // FIXME
    $this->name= '\\'.$this->backing->name;
    $this->parent= ($p= $this->backing->getParentClass()) ? '\\'.$p->name : null;
  }

  public function name() { return $this->backing->name; }

  public function constant($name) {
    if (!$this->backing->hasConstant($name)) return null;

    $type= null; // $p->getType(); PHP 7.4.0 ***OR*** APIDOC?!

    $f= function($scope) use($name) { return $this->backing->getConstant($name); };
    $r= new Constant([], $name, $type, new Resolveable($f));
    $r->holder= $this->backing->name;
    return $r;
  }

  public function property($name) {
    if (!$this->backing->hasProperty($name)) return null;

    $p= $this->backing->getProperty($name);
    $type= null; // $p->getType(); PHP 7.4.0 ***OR*** APIDOC?!

    if ($p->isStatic()) {
      $f= function($scope) use($p) { return $p->getValue(null); };
    } else {
      $f= function($scope) { throw new IllegalStateException('Cannot resolve'); };
    }
    $r= new Property($p->getModifiers(), $p->name, $type, new Resolveable($f), [], $p->getDocComment());
    $r->holder= $this->backing->name;
    return $r;
  }
}