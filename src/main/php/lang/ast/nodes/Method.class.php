<?php namespace lang\ast\nodes;

class Method extends Annotated implements Member {
  public $name, $modifiers, $signature, $annotations, $body, $comment;

  public function __construct($modifiers, $name, $signature, $body= null, $annotations= [], $comment= null) {
    $this->name= $name;
    $this->modifiers= $modifiers;
    $this->signature= $signature;
    $this->body= $body;
    $this->annotations= $annotations;
    $this->comment= $comment;
  }

  /** @return string */
  public function kind() { return 'method'; }

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
}