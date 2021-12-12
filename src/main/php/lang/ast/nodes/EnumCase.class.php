<?php namespace lang\ast\nodes;

class EnumCase extends Annotated implements Member {
  public $kind= 'enumcase';
  public $name, $expression, $holder;

  public function __construct($name, $expression, $annotations, $comment, $line= -1, $holder= null) {
    $this->name= $name;
    $this->expression= $expression;
    $this->annotations= $annotations;
    $this->line= $line;
    $this->holder= $holder;
    $comment === null || $this->attach($comment);
  }

  /** @return string */
  public function lookup() { return $this->name; }

  /**
   * Checks whether this node is of a given kind
   *
   * @param  string $kind
   * @return bool
   */
  public function is($kind) {
    return $this->kind === $kind || '@member' === $kind || parent::is($kind);
  }
}