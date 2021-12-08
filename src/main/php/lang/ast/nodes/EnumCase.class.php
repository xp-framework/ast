<?php namespace lang\ast\nodes;

class EnumCase extends Annotated implements Member {
  public $kind= 'enumcase';
  public $name, $expression;
  public $holder= null;

  public function __construct($name, $expression, $annotations, $line= -1) {
    $this->name= $name;
    $this->expression= $expression;
    $this->annotations= $annotations;
    $this->line= $line;
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