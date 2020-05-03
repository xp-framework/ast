<?php namespace lang\ast\types;

use lang\Value;
use util\Objects;

class Compiled implements Value {
  private $type, $inherit;

  public function __construct($type, $scope) {
    $this->type= $type;

    $this->inherit= function() use($scope) {
      $this->inherit= [];

      // Everything from parent
      if (isset($this->type->parent)) {
        $parent= $scope->type($this->type->parent);
        foreach ($parent->members() as $lookup => $member) {
          isset($this->inherit[$lookup]) || $this->inherit[$lookup]= $member;
        }
      }

      // Everything from traits (FIXME: ALIASES!)
      foreach ($this->type->traits() as $name) {
        $trait= $scope->type($name);
        foreach ($trait->members() as $lookup => $member) {
          isset($this->inherit[$lookup]) || $this->inherit[$lookup]= $member;
        }
      }

      // Interface constants
      foreach ($this->type->interfaces() as $name) {
        $interface= $scope->type($name);
        foreach ($interface->constants() as $name => $member) {
          isset($this->inherit[$name]) || $this->inherit[$name]= $member;
        }
      }
    };

    // FIXME
    $this->name= $type->name;
    $this->parent= $type->parent ?? null;
  }

  public function toString() {
    return nameof($this).'@'.Objects::stringOf($this->type);
  }

  public function hashCode() {
    return md5($this->type->name);
  }

  public function compareTo($value) {
    return $value instanceof self ? Objects::compare($this->type, $value->type) : 1;
  }

  public function name() { return $this->backing->name(); }

  public function members() {
    yield from $this->type->members();
  }

  public function constants() {
    yield from $this->type->constants();

    if ($this->inherit instanceof \Closure) {
      $f= $this->inherit;
      $f();
    }

    foreach ($this->inherit as $node) {
      if ('const' === $node->kind) yield $node->name => $node;
    }
  }

  public function constant($name) {
    if ($c= $this->type->constant($name)) return $c;

    if ($this->inherit instanceof \Closure) {
      $f= $this->inherit;
      $f();
    }

    return $this->inherit[$name] ?? null;
  }

  public function property($name) {
    if ($c= $this->type->property($name)) return $c;

    if ($this->inherit instanceof \Closure) {
      $f= $this->inherit;
      $f();
    }

    $l= '$'.$name;
    return $this->inherit[$l] ?? null;
  }
}