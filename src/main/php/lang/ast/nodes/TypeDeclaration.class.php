<?php namespace lang\ast\nodes;

abstract class TypeDeclaration extends Annotated {
  public $name, $modifiers, $body, $annotations, $comment;

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
    return substr($this->name, 1);
  }

  /**
   * Returns class declaration
   *
   * @return string
   */
  public function declaration() {
    return substr($this->name, strrpos($this->name, '\\') + 1);
  }

  public function parent() { return null; }

  public function interfaces() { return []; }

  /**
   * Overwrite a given member; if it is already present, replace.
   *
   * @param  lang.ast.nodes.Member $member
   * @return bool Whether anything was overwritten
   */
  public function overwrite(Member $member) {
    $lookup= $member->lookup();
    $overwritten= isset($this->body[$lookup]);

    $this->body[$lookup]= $member;
    return $overwritten;
  }

  /**
   * Inject a given member; if it is already present, do not touch.
   *
   * @param  lang.ast.nodes.Member $member
   * @return bool Whether anything was injected
   */
  public function inject(Member $member) {
    $lookup= $member->lookup();
    if (isset($this->body[$lookup])) return false;

    $this->body[$lookup]= $member;
    return true;
  }

  /** @return iterable */
  public function methods() {
    foreach ($this->body as $node) {
      if ('method' === $node->kind) yield $node;
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
      if ('property' === $node->kind) yield $node;
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
      if ('const' === $node->kind) yield $node;
    }
  }

  /**
   * Returns a constant
   *
   * @param  string $name
   * @return lang.ast.nodes.ConstValue or NULL
   */
  public function constant($name) {
    return isset($this->body[$name]) ? $this->body[$name] : null;
  }
}