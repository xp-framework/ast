<?php namespace lang\ast\nodes;

class Method extends Annotated implements Member {
  public $kind= 'method';
  public $name, $modifiers, $signature, $body, $holder;

  public function __construct($modifiers, $name, $signature, $body= null, $annotations= null, $comment= null, $line= -1, $holder= null) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->signature= $signature;
    $this->body= $body;
    $this->holder= $holder;

    parent::__construct($annotations, $comment, $line);
  }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return $this->kind === $kind || '@member' === $kind || parent::is($kind);
  }

  /** @return string */
  public function lookup() { return $this->name.'()'; }

  /**
   * Prepend a node to the method body
   *
   * @param  lang.ast.Node $node
   * @return void
   */
  public function prepend($node) {
    array_unshift($this->body, $node);
  }

  /**
   * Append a node to the method body
   *
   * @param  lang.ast.Node $node
   * @return void
   */
  public function append($node) {
    $this->body[]= $node;
  }

  /** @return iterable */
  public function children() { return (array)$this->body; }
}