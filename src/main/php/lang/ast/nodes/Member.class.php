<?php namespace lang\ast\nodes;

interface Member {

  /** @return string */
  public function kind();

  /** @return string */
  public function lookup();
}