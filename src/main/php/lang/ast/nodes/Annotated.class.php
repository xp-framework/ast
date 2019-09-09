<?php namespace lang\ast\nodes;

use lang\ast\Node;

abstract class Annotated extends Node {
  public $annotations;

  /**
   * Returns an annotation for a given name, or NULL if no annotation
   * exists by that name.
   *
   * @param  string $name
   * @return lang.ast.nodes.Annotation
   */
  public function annotation($name) {
    return array_key_exists($name, $this->annotations)
      ? new Annotation($name, $this->annotations[$name])
      : null
    ;
  }
}