<?php namespace lang\ast\nodes;

class Property extends Annotated implements Member {
  public $kind= 'property';
  public $holder;
  public $name, $modifiers, $expression, $type, $annotations, $comment;

  public function __construct($modifiers, $name, $type, $expression= null, $annotations= [], $comment= null, $line= -1) {
    $this->modifiers= $modifiers;
    $this->name= $name;
    $this->type= $type;
    $this->expression= $expression;
    $this->annotations= $annotations;
    $this->comment= $comment;
    $this->line= $line;
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
