<?php namespace lang\mirrors;

use lang\mirrors\parse\{ClassSource, ClassSyntax, Value};
use lang\{ElementNotFoundException, Enum, Error, IllegalArgumentException, IllegalStateException, Throwable, Type, XPClass};

class FromReflection implements Source {
  protected $reflect;
  private $source;
  private $unit= null;
  public $name;

  private static $RETAIN_COMMENTS, $VARIADIC_SUPPORTED;

  static function __static() {
    $save= ini_get('opcache.save_comments');
    self::$RETAIN_COMMENTS= false === $save ? true : (bool)$save;
    self::$VARIADIC_SUPPORTED= method_exists(\ReflectionParameter::class, 'isVariadic');
  }

  /**
   * Creates a new reflection source
   *
   * @param  php.ReflectionClass $reflect
   * @param  lang.mirrors.Sources $source
   */
  public function __construct(\ReflectionClass $reflect, Sources $source= null) {
    $this->reflect= $reflect;
    $this->name= $reflect->name;
    $this->source= $source ?: Sources::$REFLECTION;
  }

  /** @return bool */
  public function present() { return true; }

  /** @return lang.mirrors.parse.CodeUnit */
  public function codeUnit() { return $this->unit ?? $this->unit= (new ClassSyntax())->codeUnitOf($this->typeName()); }

  /** @return string */
  public function typeName() { return strtr($this->name, '\\', '.'); }

  /** @return string */
  public function typeDeclaration() { return $this->reflect->getShortName(); }

  /** @return lang.Type */
  public function typeInstance() { return new XPClass($this->reflect); }

  /** @return string */
  public function packageName() { return strtr($this->reflect->getNamespaceName(), '\\', '.'); }

  /** @return self */
  public function typeParent() {
    $parent= $this->reflect->getParentClass();
    return $parent ? $this->source->reflect($parent) : null;
  }

  /** @return string */
  public function typeComment() {
    if (self::$RETAIN_COMMENTS) {
      $comment= $this->reflect->getDocComment();
      return false === $comment ? null : trim(preg_replace('/\n\s+\* ?/', "\n", substr($comment, 3, -2)));
    } else {
      return $this->codeUnit()->typeComment();
    }
  }

  /**
   * Maps reflection type
   *
   * @param  php.ReflectionMethod|php.ReflectionParameter $reflect
   * @param  string $name
   * @return php.Closure
   */
  private function mapReflectionType($reflect, $name) {
    if ('self' === $name) {
      return function() use($reflect) { return new XPClass($reflect->getDeclaringClass()); };
    } else if ('parent' === $name) {
      return function() use($reflect) {
        if ($parent= $reflect->getDeclaringClass()->getParentClass()) return new XPClass($parent);
        throw new IllegalStateException('Cannot resolve parent type of class without parent');
      };
    } else {
      return function() use($name) { return Type::forName($name); };
    }
  }

  /**
   * Extracts annotations from compiled meta information
   *
   * @param  [:var] $compiled
   * @param  [:lang.mirrors.parse.Value] $annotations
   */
  private function annotationsOf($compiled) {
    $annotations= [];
    foreach ($compiled as $name => $value) {
      $annotations[$name]= null === $value ? null : new Value($value);
    }
    return $annotations;
  }

  /** @return var */
  public function typeAnnotations() {
    $class= $this->typeName();
    $details= XPClass::detailsForClass($class);
    if ($details && $details['class'][DETAIL_ANNOTATIONS]) {
      return $this->annotationsOf($details['class'][DETAIL_ANNOTATIONS]);
    } else {
      return null;
    }
  }

  /** @return lang.mirrors.Modifiers */
  public function typeModifiers() {
    $modifiers= Modifiers::IS_PUBLIC | ($this->reflect->isInternal() ? Modifiers::IS_NATIVE : 0);

    // HHVM and PHP differ in this. We'll handle traits as *always* abstract (needs
    // to be implemented) and *never* final (couldn't be implemented otherwise).
    if ($this->reflect->isTrait()) {
      return new Modifiers($modifiers | Modifiers::IS_ABSTRACT);
    } else {
      $m= $this->reflect->getModifiers();
      $m & \ReflectionClass::IS_EXPLICIT_ABSTRACT && $modifiers |= Modifiers::IS_ABSTRACT;
      $m & \ReflectionClass::IS_IMPLICIT_ABSTRACT && $modifiers |= Modifiers::IS_ABSTRACT;
      $m & \ReflectionClass::IS_FINAL && $modifiers |= Modifiers::IS_FINAL;
      return new Modifiers($modifiers);
    }
  }

  /** @return lang.mirrors.Kind */
  public function typeKind() {
    if ($this->reflect->isTrait()) {
      return Kind::$TRAIT;
    } else if ($this->reflect->isInterface()) {
      return Kind::$INTERFACE;
    } else if ($this->reflect->isSubclassOf(Enum::class)) {
      return Kind::$ENUM;
    } else {
      return Kind::$CLASS;
    }
  }

  /**
   * Returns whether this type is a subtype of a given argument
   *
   * @param  string $class
   * @return bool
   */
  public function isSubtypeOf($class) {
    return $this->reflect->isSubclassOf($class);
  }

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return  bool
   */
  public function typeImplements($name) {
    return $this->reflect->implementsInterface($name);
  }

  /** @return iterable */
  public function allInterfaces() {
    foreach ($this->reflect->getInterfaces() as $interface) {
      yield $interface->name => $this->source->reflect($interface);
    }
  }

  /** @return iterable */
  public function declaredInterfaces() {
    $parent= $this->reflect->getParentClass();
    $inherited= $parent ? array_flip($parent->getInterfaceNames()) : [];
    $local= $this->reflect->getInterfaces();
    foreach ($local as $interface) {
      if (isset($inherited[$interface->name])) continue;
      foreach ($local as $compare) {
        if ($compare->isSubclassOf($interface)) continue 2;
      }
      yield $interface->name => $this->source->reflect($interface);
    }
  }

  /** @return iterable */
  public function allTraits() {
    $reflect= $this->reflect;
    do {
      foreach ($reflect->getTraits() as $trait) {
        yield $trait->name => $this->source->reflect($trait);
      }
    } while ($reflect= $reflect->getParentClass());
  }

  /** @return iterable */
  public function declaredTraits() {
    foreach ($this->reflect->getTraits() as $trait) {
      yield $trait->name => $this->source->reflect($trait);
    }
  }

  /**
   * Returns whether this type implements a given interface
   *
   * @param  string $name
   * @return bool
   */
  public function typeUses($name) {
    $reflect= $this->reflect;
    do {
      if (in_array($name, $reflect->getTraitNames(), true)) return true;
    } while ($reflect= $reflect->getParentClass());
    return false;
  }

  /** @return [:var] */
  public function constructor() {

    // Do not use getConstructor() as in some situations this fails in PHP 7.3 (!)
    if ($this->reflect->hasMethod('__construct')) {
      return $this->method($this->reflect->getMethod('__construct'));
    }

    return [
      'name'        => '__default',
      'access'      => new Modifiers(Modifiers::IS_PUBLIC),
      'holder'      => $this->reflect->name,
      'comment'     => function() { return null; },
      'params'      => function() { return []; },
      'annotations' => function() { return []; },
    ];
  }

  /**
   * Creates a new instance
   *
   * @param  var[] $args
   * @return var
   */
  public function newInstance($args) {
    if (!$this->reflect->isInstantiable()) {
      throw new IllegalArgumentException('Verifying '.$this->name.': Cannot instantiate');
    }

    try {
      return $this->reflect->newInstanceArgs($args);
    } catch (Throwable $e) {
      throw new TargetInvocationException('Creating a new instance of '.$this->name.' raised '.nameof($e), $e);
    } catch (\ReflectionException $e) {
      throw new IllegalArgumentException('Instantiating '.$this->name.': '.$e->getMessage());
    } catch (\Exception $e) {
      throw new TargetInvocationException('Creating a new instance of '.$this->name.' raised '.get_class($e), new Error($e->getMessage()));
    } catch (\Throwable $e) {
      throw new TargetInvocationException('Creating a new instance of '.$this->name.' raised '.get_class($e), new Error($e->getMessage()));
    }
  }

  /**
   * Finds the member declaration
   *
   * @param  lang.mirrors.Source $declaredIn
   * @param  string $kind
   * @param  string $name
   * @return [:var]
   */
  private function memberDeclaration($declaredIn, $kind, $name) {
    if ($declaredIn->typeModifiers()->isNative()) {
      return null;
    } else {
      $declaration= $declaredIn->codeUnit()->declaration();
      if (isset($declaration[$kind][$name])) return $declaration[$kind][$name];

      foreach ($declaredIn->allTraits() as $trait) {
        $declaration= $trait->codeUnit()->declaration();
        if (isset($declaration[$kind][$name])) return $declaration[$kind][$name];
      }

      throw new IllegalStateException('The '.$kind.' declaration of '.$declaredIn->name.'::'.$name.' could not be located');
    }
  }

  /**
   * Checks whether a given field exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasField($name) { return $this->reflect->hasProperty($name); }

  /**
   * Maps annotations
   *
   * @param  php.ReflectionProperty $reflect
   * @return [:var]
   */
  protected function fieldAnnotations($reflect) {
    $details= XPClass::detailsForField($reflect->getDeclaringClass(), $reflect->name);
    return isset($details[DETAIL_ANNOTATIONS])
      ? $this->annotationsOf($details[DETAIL_ANNOTATIONS])
      : null
    ;
  }

  /**
   * Maps a field
   *
   * @param  php.ReflectionProperty $reflect
   * @return [:var]
   */
  protected function field($reflect) {
    return [
      'name'        => $reflect->name,
      'access'      => new Modifiers($reflect->getModifiers() & ~0x1fb7f008),
      'holder'      => $reflect->getDeclaringClass()->name,
      'annotations' => function() use($reflect) { return $this->fieldAnnotations($reflect); },
      'read'        => function($instance) use($reflect) { return $this->readField($reflect, $instance); },
      'modify'      => function($instance, $value) use($reflect) { $this->modifyField($reflect, $instance, $value); },
      'comment'     => function() use($reflect) {
        if (self::$RETAIN_COMMENTS) {
          $comment= $reflect->getDocComment();
          return false === $comment ? null : trim(preg_replace('/\n\s+\* ?/', "\n", substr($comment, 3, -2)));
        } else {
          $field= $this->memberDeclaration($this->resolve('\\'.$reflect->getDeclaringClass()->name), 'field', $reflect->name);
          return $field['comment'] ?? null;
        }
      }
    ];
  }

  /**
   * Reads a field
   *
   * @param  php.ReflectionProperty $reflect
   * @param  var $instance
   * @return var
   */
  private function readField($reflect, $instance) {
    $reflect->setAccessible(true);
    if ($reflect->isStatic()) {
      return $reflect->getValue(null);
    } else if ($instance && $reflect->getDeclaringClass()->isInstance($instance)) {
      return $reflect->getValue($instance);
    }

    throw new IllegalArgumentException(sprintf(
      'Verifying %s(): Object passed is not an instance of the class declaring this field',
      $reflect->name
    ));
  }

  /**
   * Modifies a field
   *
   * @param  php.ReflectionProperty $reflect
   * @param  var $instance
   * @param  var $value
   * @return void
   */
  private function modifyField($reflect, $instance, $value) {
    $reflect->setAccessible(true);
    if ($reflect->isStatic()) {
      $reflect->setValue(null, $value);
      return;
    } else if ($instance && $reflect->getDeclaringClass()->isInstance($instance)) {
      $reflect->setValue($instance, $value);
      return;
    }

    throw new IllegalArgumentException(sprintf(
      'Verifying %s(): Object passed is not an instance of the class declaring this field',
      $reflect->name
    ));
  }

  /**
   * Gets a field by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function fieldNamed($name) {
    try {
      return $this->field($this->reflect->getProperty($name));
    } catch (\Exception $e) {
      throw new ElementNotFoundException('No field named $'.$name.' in '.$this->name);
    }
  }

  /** @return iterable */
  public function allFields() {
    foreach ($this->reflect->getProperties() as $field) {
      yield $field->name => $this->field($field);
    }
  }

  /** @return iterable */
  public function declaredFields() {
    foreach ($this->reflect->getProperties() as $field) {
      if ($field->getDeclaringClass()->name === $this->reflect->name) {
        yield $field->name => $this->field($field);
      }
    }
  }

  /**
   * Maps annotations
   *
   * @param  php.ReflectionParameter $reflect
   * @return [:var]
   */
  protected function paramAnnotations($reflect) {
    $target= '$'.$reflect->name;
    $details= XPClass::detailsForMethod($reflect->getDeclaringClass(), $reflect->getDeclaringFunction()->name);
    return isset($details[DETAIL_TARGET_ANNO][$target])
      ? $this->annotationsOf($details[DETAIL_TARGET_ANNO][$target])
      : null
    ;
  }

  /**
   * Maps a parameter
   *
   * @param  int $pos
   * @param  php.ReflectionParameter $reflect
   * @return [:var]
   */
  protected function param($pos, $reflect) {
    if ($t= $reflect->getType()) {
      $type= $this->mapReflectionType($reflect, PHP_VERSION_ID >= 70100 ? $t->getName() : $t->__toString());
    } else {
      $type= null;
    }

    if ($reflect->isVariadic()) {
      $var= true;
      $default= null;
    } else if ($reflect->isOptional()) {
      $var= null;
      $default= function() use($reflect) { return $reflect->getDefaultValue(); };
    } else {
      $var= false;
      $default= null;
    }

    return [
      'pos'         => $pos,
      'name'        => $reflect->name,
      'type'        => $type,
      'ref'         => $reflect->isPassedByReference(),
      'default'     => $default,
      'var'         => $var,
      'annotations' => function() use($reflect) { return $this->paramAnnotations($reflect); }
    ];
  }

  /**
   * Maps annotations
   *
   * @param  php.ReflectionMethod $reflect
   * @return [:var]
   */
  protected function methodAnnotations($reflect) {
    $details= XPClass::detailsForMethod($reflect->getDeclaringClass(), $reflect->name);
    return isset($details[DETAIL_ANNOTATIONS])
      ? $this->annotationsOf($details[DETAIL_ANNOTATIONS])
      : null
    ;
  }

  /**
   * Maps a method
   *
   * @param  php.ReflectionMethod $reflect
   * @return [:var]
   */
  protected function method($reflect) {
    $returns= $reflect->getReturnType();
    return [
      'name'        => $reflect->name,
      'access'      => new Modifiers($reflect->getModifiers() & ~0x1fb7f008),
      'holder'      => $reflect->getDeclaringClass()->name,
      'params'      => function() use($reflect) {
        $params= [];
        foreach ($reflect->getParameters() as $pos => $param) {
          $params[]= $this->param($pos, $param);
        }
        return $params;
      },
      'returns'     => $returns
        ? $this->mapReflectionType($reflect, PHP_VERSION_ID >= 70100 ? $returns->getName() : $returns->__toString())
        : null
      ,
      'annotations' => function() use($reflect) { return $this->methodAnnotations($reflect); },
      'invoke'      => function($instance, $args) use($reflect) { return $this->invokeMethod($reflect, $instance, $args); },
      'comment'     => function() use($reflect) {
        if (self::$RETAIN_COMMENTS) {
          $comment= $reflect->getDocComment();
          return false === $comment ? null : trim(preg_replace('/\n\s+\* ?/', "\n", substr($comment, 3, -2)));
        } else {
          $method= $this->memberDeclaration($this->resolve('\\'.$reflect->getDeclaringClass()->name), 'method', $reflect->name);
          return $method['comment'] ?? null;
        }
      }
    ];
  }

  /**
   * Checks whether a given method exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasMethod($name) { return $this->reflect->hasMethod($name); }

  /**
   * Invokes the method
   *
   * @param  php.ReflectionMethod $reflect
   * @param  var $instance
   * @param  var[] $args
   * @return var
   */
  private function invokeMethod($reflect, $instance, $args) {
    $reflect->setAccessible(true);
    try {
      return $reflect->invokeArgs($instance, $args);
    } catch (Throwable $e) {
      throw new TargetInvocationException('Invoking '.$reflect->name.'() raised '.nameof($e), $e);
    } catch (\ReflectionException $e) {
      throw new IllegalArgumentException('Verifying '.$reflect->name.'(): '.$e->getMessage());
    } catch (\Exception $e) {
      throw new TargetInvocationException('Invoking '.$reflect->name.'() raised '.get_class($e), new Error($e->getMessage()));
    } catch (\Throwable $e) {
      throw new TargetInvocationException('Invoking '.$reflect->name.'() raised '.get_class($e), new Error($e->getMessage()));
    }
  }

  /**
   * Gets a method by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function methodNamed($name) { 
    try {
      return $this->method($this->reflect->getMethod($name));
    } catch (\Exception $e) {
      throw new ElementNotFoundException('No method named '.$name.'() in '.$this->name);
    }
  }

  /** @return iterable */
  public function allMethods() {
    foreach ($this->reflect->getMethods() as $method) {
      yield $method->name => $this->method($method);
    }
  }

  /** @return iterable */
  public function declaredMethods() {
    foreach ($this->reflect->getMethods() as $method) {
      if ($method->getDeclaringClass()->name === $this->reflect->name) {
        yield $method->name => $this->method($method);
      }
    }
  }

  /**
   * Checks whether a given constant exists
   *
   * @param  string $name
   * @return bool
   */
  public function hasConstant($name) { return $this->reflect->hasConstant($name); }

  /**
   * Gets a constant by its name
   *
   * @param  string $name
   * @return var
   * @throws lang.ElementNotFoundException
   */
  public function constantNamed($name) {
    if ($this->reflect->hasConstant($name)) {
      return $this->reflect->getConstant($name);
    }
    throw new ElementNotFoundException('No constant named '.$name.'() in '.$this->name);
  }

  /** @return iterable */
  public function allConstants() {
    foreach ($this->reflect->getConstants() as $name => $value) {
      yield $name => $value;
    }
  }

  /**
   * Resolves a type name in the context of this reflection source
   *
   * @param  string $name
   * @return self
   */
  public function resolve($name) {
    if ('self' === $name || $name === $this->reflect->getShortName()) {
      return $this->source->reflect($this->reflect);
    } else if ('parent' === $name) {
      if ($parent= $this->reflect->getParentClass()) return $this->source->reflect($parent);
      throw new IllegalStateException('Cannot resolve parent type of class without parent');
    } else if ('\\' === $name[0]) {
      return $this->source->reflect(strtr(substr($name, 1), '.', '\\'));
    } else if (strstr($name, '\\')) {
      $ns= $this->reflect->getNamespaceName();
      return $this->source->reflect(($ns ? $ns.'\\' : '').$name);
    } else if (strstr($name, '.')) {
      return $this->source->reflect(strtr($name, '.', '\\'));
    } else {
      $imports= $this->codeUnit()->imports();
      if (isset($imports[$name])) return $this->source->reflect($imports[$name]);
      $ns= $this->reflect->getNamespaceName();
      return $this->source->reflect(($ns ? $ns.'\\' : '').$name);
    }
  }

  /**
   * Compares a given value to this source
   *
   * @param  var $value
   * @return int
   */
  public function compareTo($value) {
    return $value instanceof self ? strcmp($this->name, $value->name) : 1;
  }

  /** @return string */
  public function hashCode() { return 'R'.md5($this->name); }

  /** @return string */
  public function toString() { return nameof($this).'<'.$this->name.'>'; }

}