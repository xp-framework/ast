<?php namespace lang\ast;

use lang\Value;

/** A symbol as used by language setup */
class Symbol implements Value {
  public $id, $lbp= 0, $std= null, $nud, $led;

  /** @return string */
  public function hashCode() {
    return $this->id;
  }

  /** @return string */
  public function toString() {
    return nameof($this).'("'.$this->id.'", lbp= '.$this->lbp.')';
  }

  /**
   * Compare
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? $this->id <=> $value->id : 1;
  }
}