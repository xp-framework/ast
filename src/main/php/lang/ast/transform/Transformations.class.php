<?php namespace lang\ast\transform;

class Transformations {
  private static $transformations= [];

  public static function register($type, $function) {
    self::$transformations[$type]= $function;
  }

  public static function registered() {
    return self::$transformations;
  }
}