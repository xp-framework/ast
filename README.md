XP AST
======

[![Build status on GitHub](https://github.com/xp-framework/compiler/workflows/Tests/badge.svg)](https://github.com/xp-framework/compiler/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)[![Latest Stable Version](https://poser.pugx.org/xp-framework/ast/version.png)](https://packagist.org/packages/xp-framework/ast)

Abstract syntax tree library used for [Compile-time metaprogramming](https://github.com/xp-framework/rfc/issues/327).

Example
-------
Register transformations to be used by the [XP Compiler](https://github.com/xp-framework/compiler). This can be done in your library's `module.xp`, for instance.

```php
use lang\ast\transform\Transformations;
use lang\ast\nodes\{Method, Signature};
use lang\ast\Code;
use codegen\Getters;

Transformations::register('class', function($codegen, $class) {
  if ($class->annotation(Getters::class)) {
    foreach ($class->properties() as $property) {
      $class->declare(new Method(
        ['public'],
        $property->name,
        new Signature([], $property->type),
        [new Code('return $this->'.$property->name)]
      ));
    }
  }
  return $class;
});
```

When compiling the following sourcecode, getters for the `id` and `name` members will automatically be added.

```php
use codegen\Getters;

#[Getters]
class Person {
  private int $id;
  private string $name;

  public function __construct(int $id, string $name) {
    $this->id= $id;
    $this->name= $name;
  }
}
```