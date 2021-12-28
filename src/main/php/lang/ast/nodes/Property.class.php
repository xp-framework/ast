<?php namespace lang\ast\nodes;

class Property extends Annotated implements Member {
  public $kind= 'property';
  public $name, $modifiers, $expression, $type, $holder;

  public function __construct($modifiers, $name, $type, $expression= null, $annotations= null, $comment= null, $line= -1, $holder= null) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->type= $type;
    $this->expression= $expression;
    $this->declared= $this->line= $line;
    $this->holder= $holder;
    null === $annotations || $this->annotate($annotations);
    null === $comment || $this->attach($comment);
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
  public function lookup() { return '$'.$this->name; }

}
