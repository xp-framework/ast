<?php namespace lang\ast\nodes;

abstract class Annotated extends Value {
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