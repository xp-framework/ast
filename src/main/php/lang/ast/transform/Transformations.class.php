<?php namespace lang\ast\transform;

class Transformations {
  private static $transformations= [];

  /**
   * Registers a transformation
   *
   * @param  string $kind
   * @param  function(lang.ast.Node): lang.ast.Node|iterable $function
   * @return int
   */
  public static function register($kind, $function) {
    self::$transformations[]= [$kind, $function];
    return sizeof(self::$transformations) - 1;
  }

  /**
   * Removes given transformations
   *
   * @param  int... $ids Returned by register()
   * @return void
   */
  public static function remove(... $ids) {
    foreach ($ids as $id) {
      unset(self::$transformations[$id]);
    }
  }

  /**
   * Returns all registered transformations, the key being the kind
   * and the value being the transformation function.
   *
   * @return iterable
   */
  public static function registered() {
    foreach (self::$transformations as $transformation) {
      yield $transformation[0] => $transformation[1];
    }
  }
}