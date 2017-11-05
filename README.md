XP AST
======

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-forge/sequence.svg)](http://travis-ci.org/xp-framework/ast)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Required PHP 5.6+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-5_6plus.png)](http://php.net/)
[![Supports PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.png)](http://php.net/)
[![Supports HHVM 3.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/hhvm-3_4plus.png)](http://hhvm.com/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/ast/version.png)](https://packagist.org/packages/xp-framework/ast)

Abstract syntax tree library used for [Compile-time metaprogramming](https://github.com/xp-framework/rfc/issues/327).

Example
-------
Register transformations to be used by the [XP Compiler](https://github.com/xp-framework/compiler). This can be done in your library's `module.xp`, for instance.

```php
use lang\ast\transform\Transformations;
use lang\ast\nodes\Method;
use lang\ast\nodes\Signature;
use lang\ast\Code;

Transformations::register('class', function($class) {
  if ($class->value->annotation('getters')) {
    foreach ($class->value->properties() as $property) {
      $class->value->inject(new Method(
        $property->name,
        ['public'],
        new Signature([], $property->type),
        [],
        [new Code('return $this->'.$property->name.';')],
        null
      ));
    }
  }
  return $class;
});
```