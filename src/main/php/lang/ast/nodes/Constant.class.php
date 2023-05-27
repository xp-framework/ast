<?php namespace lang\ast\nodes;

class Constant extends Annotated implements Member {
  public $kind= 'const';
  public $name, $modifiers, $expression, $type;

  public function __construct($modifiers, $name, $type, $expression, $annotations= null, $comment= null, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->type= $type;
    $this->expression= $expression;
    parent::__construct($annotations, $comment, $line);
  }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return $this->kind === $kind || '@member' === $kind;
  }

  /** @return string */
  public function lookup() { return $this->name; }

}