<?php namespace lang\ast\nodes;

use lang\ast\Type;
use lang\ast\types\IsGeneric;

abstract class TypeDeclaration extends Annotated {
  public $modifiers, $name, $body;

  public function __construct($modifiers, $name, $body= [], $annotations= null, $comment= null, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->body= [];
    foreach ($body as $lookup => $node) {
      $node->holder= $this->name;
      $this->body[$lookup]= $node;
    }

    parent::__construct($annotations, $comment, $line);
  }

  /** @return iterable */
  public function children() { return $this->body; }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return $this->kind === $kind || '@type' === $kind || parent::is($kind);
  }

  /**
   * Returns class name (excluding the leading backslash)
   *
   * @return string
   */
  public function name() {
    return substr($this->name->literal(), 1);
  }

  /**
   * Returns class declaration
   *
   * @return string
   */
  public function declaration() {
    $literal= $this->name instanceof IsGeneric ? $this->name->base->literal() : $this->name->literal();
    return substr($literal, strrpos($literal, '\\') + 1);
  }

  public function parent() { return null; }

  public function interfaces() { return []; }

  /** @return iterable */
  public function traits() {
    foreach ($this->body as $node) {
      if ('use' === $node->kind) yield from $node->types;
    }
  }

  /**
   * Declare a given member
   *
   * @param  lang.ast.nodes.Member $member
   * @return bool Whether anything was declared
   */
  public function declare(Member $member) {
    $lookup= $member->lookup();
    if (isset($this->body[$lookup])) return false;

    $member->holder= $this->name;
    $this->body[$lookup]= $member;
    return true;
  }

  /**
   * Overwrite a given member; if it is already present, replace.
   *
   * @param  lang.ast.nodes.Member $member
   * @return bool Whether anything was overwritten
   */
  public function overwrite(Member $member) {
    $lookup= $member->lookup();
    $overwritten= isset($this->body[$lookup]);

    $member->holder= $this->name;
    $this->body[$lookup]= $member;
    return $overwritten;
  }

  /** @return iterable */
  public function members() {
    foreach ($this->body as $lookup => $node) {
      if ($node->is('@member')) yield $lookup => $node;
    }
  }

  /** @return iterable */
  public function methods() {
    foreach ($this->body as $node) {
      if ('method' === $node->kind) yield $node->name => $node;
    }
  }

  /**
   * Returns a method
   *
   * @param  string $name
   * @return lang.ast.nodes.MethodValue or NULL
   */
  public function method($name) {
    $lookup= $name.'()';
    return isset($this->body[$lookup]) ? $this->body[$lookup] : null;
  }

  /** @return iterable */
  public function properties() {
    foreach ($this->body as $node) {
      if ('property' === $node->kind) yield $node->name => $node;
    }
  }

  /**
   * Returns a property
   *
   * @param  string $name
   * @return lang.ast.nodes.PropertyValue or NULL
   */
  public function property($name) {
    $lookup= '$'.$name;
    return isset($this->body[$lookup]) ? $this->body[$lookup] : null;
  }

  /** @return iterable */
  public function constants() {
    foreach ($this->body as $node) {
      if ('const' === $node->kind) yield $node->name => $node;
    }
  }

  /**
   * Returns a constant
   *
   * @param  string $name
   * @return lang.ast.nodes.Constant or NULL
   */
  public function constant($name) {
    return isset($this->body[$name]) ? $this->body[$name] : null;
  }
}