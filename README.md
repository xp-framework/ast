XP AST
======

[![Build status on GitHub](https://github.com/xp-framework/ast/workflows/Tests/badge.svg)](https://github.com/xp-framework/ast/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.4+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_4plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-framework/ast/version.svg)](https://packagist.org/packages/xp-framework/ast)

Abstract syntax tree library used for [XP Compiler](https://github.com/xp-framework/compiler).

Example
-------
```php
use lang\ast\{Language, Tokens};

$tree= Language::named('PHP')->parse(new Tokens('echo PHP_VERSION;'))->tree();

// lang.ast.ParseTree(source: (string))@{
//   scope => lang.ast.Scope {
//     parent => null
//     package => null
//     imports => []
//     types => []
//   }
//   children => [lang.ast.nodes.EchoStatement {
//     kind => "echo"
//     expressions => [lang.ast.nodes.Literal {
//       kind => "literal"
//       expression => "PHP_VERSION"
//       line => 1
//     }]
//     line => 1
//   }]
// }
```

Compile-time metaprogramming
----------------------------
Register transformations by creating classes inside the `lang.ast.syntax.php` package - see https://github.com/xp-framework/rfc/issues/327


```php
namespace lang\ast\syntax\php;

use lang\ast\Code;
use lang\ast\nodes\{Method, Signature};
use lang\ast\syntax\Extension;
use codegen\Getters;

class CreateGetters implements Extension {

  public function setup($language, $emitter) {
    $emitter->transform('class', function($codegen, $class) {
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
  }
}
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