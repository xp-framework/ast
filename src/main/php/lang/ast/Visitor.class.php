<?php namespace lang\ast;

abstract class Visitor {

  /**
   * Visits annotations
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function annotation($self) { }

  /**
   * Visits array literals
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function array($self) { }

  /**
   * Visits assignments
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function assignment($self) { }

  /**
   * Visits binary operators
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function binary($self) { }

  /**
   * Visits blocks
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function block($self) { }

  /**
   * Visits braced expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function braced($self) { }

  /**
   * Visits break statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function break($self) { }

  /**
   * Visits case statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function case($self) { }

  /**
   * Visits casts
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function cast($self) { }

  /**
   * Visits classes
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function class($self) { }

  /**
   * Visits closures
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function closure($self) { }

  /**
   * Visits constants
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function const($self) { }

  /**
   * Visits continue statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function continue($self) { }

  /**
   * Visits do statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function do($self) { }

  /**
   * Visits echo statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function echo($self) { }

  /**
   * Visits for statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function for($self) { }

  /**
   * Visits foreach statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function foreach($self) { }

  /**
   * Visits function declarations
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function function($self) { }

  /**
   * Visits goto statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function goto($self) { }

  /**
   * Visits if statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function if($self) { }

  /**
   * Visits instance operators
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function instance($self) { }

  /**
   * Visits instanceof statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function instanceof($self) { }

  /**
   * Visits interface declarations
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function interface($self) { }

  /**
   * Visits invoke expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function invoke($self) { }

  /**
   * Visits labels
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function label($self) { }

  /**
   * Visits lambdas
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function lambda($self) { }

  /**
   * Visits literals
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function literal($self) { }

  /**
   * Visits methods
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function method($self) { }

  /**
   * Visits namespaces
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function namespace($self) { }

  /**
   * Visits anonymous class expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function newclass($self) { }

  /**
   * Visits new expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function new($self) { }

  /**
   * Visits offset expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function offset($self) { }

  /**
   * Visits parameters
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function parameter($self) { }

  /**
   * Visits properties
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function property($self) { }

  /**
   * Visits return statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function return($self) { }

  /**
   * Visits scope expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function scope($self) { }

  /**
   * Visits signatures
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function signature($self) { }

  /**
   * Visits starting statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function start($self) { }

  /**
   * Visits static statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function static($self) { }

  /**
   * Visits switch statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function switch($self) { }

  /**
   * Visits ternary operators
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function ternary($self) { }

  /**
   * Visits throw expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function throwexpression($self) { }

  /**
   * Visits throw statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function throw($self) { }

  /**
   * Visits trait declarations
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function trait($self) { }

  /**
   * Visits try statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function try($self) { }

  /**
   * Visits unpack expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function unpack($self) { }

  /**
   * Visits use expressions
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function use($self) { }

  /**
   * Visits import statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function import($self) { }

  /**
   * Visits using statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function using($self) { }

  /**
   * Visits variables
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function variable($self) { }

  /**
   * Visits while statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function while($self) { }

  /**
   * Visits yield statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function yield($self) { }

  /**
   * Visits from statements
   *
   * @param  lang.ast.Node $self
   * @return var
   */
  public function from($self) { }
  
}