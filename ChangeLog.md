XP AST ChangeLog
================

## ?.?.? / ????-??-??

## 11.7.1 / 2025-07-06

* Fixed more than one annotation on an element only yielding the last.
  (@thekid)

## 11.7.0 / 2025-06-22

* Merged PR #56: Implement "clone with" syntax using a `CloneExpression`
  node, see https://wiki.php.net/rfc/clone_with_v2
  (@thekid)

## 11.6.0 / 2025-05-28

* Merged PR #52: Add syntactical support for pipelines with `|>` and `?|>`,
  see https://wiki.php.net/rfc/pipe-operator-v3
  (@thekid)

## 11.5.0 / 2025-04-05

* Added syntactic support for `final` promoted parameters, see this RFC:
  https://wiki.php.net/rfc/final_promotion
  (@thekid)

## 11.4.0 / 2025-01-26

* Merged PR #55: Add suport for heredoc (and its nowdoc variant) - @thekid

## 11.3.1 / 2024-11-02

* Fixed strings enclosed in backticks - @thekid

## 11.3.0 / 2024-08-26

* Merged PR #54: Add syntactic support for asymmetric visibility, see
  https://wiki.php.net/rfc/asymmetric-visibility-v2, targeted for PHP 8.4
  (@thekid)

## 11.2.1 / 2024-08-11

* Fixed grouped `use` statements containing aliases not scoped correctly
  (@thekid)

## 11.2.0 / 2024-08-04

* Merged PR #53: Add syntactic support for `T<string>::class` - @thekid

## 11.1.0 / 2024-06-15

* Merged PR #45: Implement property hooks syntax - @thekid

## 11.0.1 / 2024-03-24

* Fixed `%=` (modulo-assignment) operator - @thekid

## 11.0.0 / 2024-03-23

* Merged PR #51: Logical Operators and Assignment Expressions - @thekid
* Made this library compatible with XP 12, droppping support for all but
  the latest PHP 7 version. Minimum PHP version required is now **7.4**!
  (@thekid)

## 10.3.1 / 2024-01-06

* Added PHP 8.4 to the test matrix - @thekid
* Fixed parsing captures and return types for closures - @thekid

## 10.3.0 / 2023-10-06

* Merged PR #49: Record starting line numbers for multi-line nodes
  (@thekid)

## 10.2.3 / 2023-10-01

* Fixed `Signature::insert()` - @thekid
* Refactored code base to use the class loading mechanism instead of the
  *Package* class from `lang.reflect`. See xp-framework/rfc#338
  (@thekid)

## 10.2.2 / 2023-07-29

* Fixed legacy `array()` syntax being parsed into an invocation instead
  of an array literal
  (@thekid)

## 10.2.1 / 2023-06-03

* Fix trailing commas in annotation lists - @thekid

## 10.2.0 / 2023-05-27

* Merged PR #47: Remove holder property for members - @thekid

## 10.1.0 / 2023-05-21

* Merged PR #46: Implement returning by reference from methods - @thekid

## 10.0.0 / 2023-04-08

* **Heads up:** Instances of `lang.ast.nodes.Variable` can contain
  the name *as well as* other variables or expressions. The *name*
  property is renamed to *pointer* to reflect this change.
  (@thekid)
* Merged PR #44: Parse expressions like `$this->{$member}` into
  `lang.ast.nodes.Expression` instances.
  (@thekid)

## 9.2.7 / 2023-03-05

* Fixed various expressions inside braces - @thekid

## 9.2.6 / 2023-02-19

* Fixed relative type names with namespace imports - @thekid

## 9.2.5 / 2023-02-19

* Fixed issue #43: Exponent notation - @thekid

## 9.2.4 / 2023-02-12

* Merged PR #41: Migrate to new testing library - @thekid
* Fixed `lang.ast.nodes.TypeDeclaration::declaration()` for generics
  (@thekid)
* Fixed `lang.ast.types.IsGeneric::literal()` not returning a fully
  qualified type name
  (@thekid)
* Fixed endless loop with syntax errors in `new` - @thekid

## 9.2.3 / 2023-02-05

* Fixed cloning of `lang.ast.Language` instances - @thekid

## 9.2.2 / 2022-12-18

* Fixed line numbers for match conditions, case labels and catch
  statements
  (@thekid)

## 9.2.1 / 2022-12-04

* Fixed type parsing in type casts:
  - Arrays, maps and generics with nullables, e.g. `(array<?int>)$v`
  - Intersection and union types, e.g. `(int|string)$v`.
  (@thekid)

## 9.2.0 / 2022-11-12

* Added support for omitting expressions in destructuring assignments,
  e.g. `list($a, , $b)= $expr` or `[, $a]= $expr`.
  (@thekid)

## 9.1.0 / 2022-11-06

* Added support for generic wildcards such as `Filter<?>`, resolving
  ambiguity with nullable types
  (@thekid)
* Resolved ambiguity between short open tag and nullables in generics,
  e.g. `Filter<?int>`.
  (@thekid)

## 9.0.0 / 2022-11-06

* Merged PR #40: Create generic member in `lang.ast.nodes.Signature`
  (@thekid)
* Merged PR #39: Refactor type declaration, parents, interfaces to
  `lang.ast.Type` instances
  (@thekid)

## 8.2.0 / 2022-09-03

* Added support for PHP 8.2 `null`, `true` and `false` types, see:
  https://wiki.php.net/rfc/null-false-standalone-types
  https://wiki.php.net/rfc/true-type
  (@thekid)

## 8.1.0 / 2022-05-14

* Merged PR #37: Implement readonly modifier for classes - @thekid

## 8.0.1 / 2022-04-03

* Fixed resolving types starting with `namespace` keyword (examples 4 and
  5 in https://www.php.net/manual/en/language.namespaces.nsconstants.php)
  (@thekid)

## 8.0.0 / 2022-01-07

This major release promotes annotations and comments to *Node* subclasses,
making it easy to implement different emitter scenarios for them. For
example, classes to be used with the XP Framework will have meta information
attached to them, while others will not, reducing their dependencies.

* Merged PR #35: Support static closures (`static fn() => ...`) - @thekid
* Implemented xp-framework/rfc#341: Drop XP 9 compatibility - @thekid
* Merged PR #34: Refactor annotations from associative arrays to instances
  of the `lang.ast.nodes.Annotations` class.
  (@thekid)
* Merged PR #33: Refactor apidoc comments from bare strings to instances
  of the `lang.ast.nodes.Comment` class.
  (@thekid)

## 7.7.2 / 2021-12-08

* Fixed PHP 8.2 warning about dynamic properties - @thekid

## 7.7.1 / 2021-10-21

* Made library compatible with XP 11 - @thekid

## 7.7.0 / 2021-10-06

* Merged PR #32: Support `new T(...)` callable syntax - @thekid

## 7.6.2 / 2021-10-01

* Fixed #31: Call to undefined method `lang\ast\syntax\PHP::expecting()`
  (@thekid)

## 7.6.1 / 2021-09-26

* Fixed `use` statement not supporting multiple imports separated by `,`.
  (@thekid)
* Fixed importing global classes into namespaces, e.g. `use Traversable`.
  (@thekid)

## 7.6.0 / 2021-09-11

* Merged PR #30: Add support for readonly properties, to both property
  declarations and constructor argument promotion - available in PHP 8.1
  See https://wiki.php.net/rfc/readonly_properties_v2
  (@thekid)

## 7.5.1 / 2021-09-06

* Fixed *Expected ":", have "::" in switch* for class constants - @thekid

## 7.5.0 / 2021-08-03

* Merged PR #28: Intersection types (see xp-framework/compiler#117 and
  https://wiki.php.net/rfc/pure-intersection-types)
  (@thekid)
* Merged PR #29: Add error source (file and line) to message - @thekid

## 7.4.0 / 2021-07-12

* Merged PR #27: Implement first-class callable syntax - @thekid

## 7.3.0 / 2021-05-22

* Merged PR #25: Add support for declare construct - @thekid

## 7.2.0 / 2021-04-25

* Merged PR #24: Add support for never return type - @thekid

## 7.1.1 / 2021-04-18

* Fixed `yield` without expression in various situations - @thekid

## 7.1.0 / 2021-03-13

* Merged PR #23: Add syntactic support for PHP 8.1 enums. Implementation
  in the compiler is in pull request xp-framework/compiler#106
  (@thekid)

## 7.0.4 / 2021-03-07

* Fixed *Call to undefined method ::emitoperator()* caused by standalone
  operators, see xp-framework/compiler#105
  (@thekid)

## 7.0.3 / 2021-02-22

* Fixed children() accessor for `match` expressions and conditions, see
  xp-framework/compiler#101
  (@thekid)
* Fixed PHP 8.1 compatiblity by ensuring we do not pass NULL to strlen()
  (@thekid)

## 7.0.2 / 2021-01-04

* Fixed argument parser to queue correctly when handling ambiguity between
  named arguments and global constants (see xp-framework/compiler#98)
  (@thekid)

## 7.0.1 / 2021-01-03

* Fixed xp-framework/compiler#98: `(fstat(STDOUT))` causes a parse error
  (@thekid)

## 7.0.0 / 2020-11-28

This major release removes quite a bit of legacy: XP and Hack language
annotations as well as support for curly braces as string or array offsets,
bringing it in line with PHP 8.

* Fixed multiple semicolons yielding syntax errors, skip them instead
  (@thekid)
* Removed support for using curly braces as offset (e.g. `$value{0}`)
  (@thekid)
* Merged PR #22: Stream tokens directly instead of using if/else cascade
  (@thekid)
* Merged PR #18: Allow match without expression: `match { ... }`. See
  https://wiki.php.net/rfc/match_expression_v2#allow_dropping_true
  (@thekid)
* Merged PR #17: Refactor parsing to allow blocks anywhere an expression
  is allowed. This not only allows `fn() => { ... }` but also using blocks
  in `match` expressions.
  (@thekid)
* Merged PR #21: Remove legacy XP annotations (`#[@annotation]`) - @thekid
* Merged PR #19: Remove support for Hack language annotations - @thekid
* Merged PR #20: Remove transformations API - @thekid

## 6.1.0 / 2020-11-22

* Added support for non-capturing catches, see this PHP 8 RFC:
  https://wiki.php.net/rfc/non-capturing_catches
  (@thekid)

## 6.0.0 / 2020-10-09

This major release removes the starting `<?php` token from the stream of
returned tokens: One less case to handle.

* Merged PR #16: Omit start token from stream - @thekid

## 5.4.0 / 2020-09-29

* Merged PR #9: Deprecate hacklang-style annotations in favor PHP 8
  attributes. This is step 2 of xp-framework/compiler#86
  (@thekid)

## 5.3.0 / 2020-09-27

* Fixed named arguments using keywords - @thekid
* Changed tokens to yield variables including the leading `$` sign
  (@thekid)
* Fixed visitor API for unary prefix (e.g. `++$i`) and unary suffix
  (e.g. `$i++`) operators
  (@thekid)

## 5.2.0 / 2020-09-12

* Merged PR #13: Add syntactic support for named arguments (PHP 8), see
  https://wiki.php.net/rfc/named_params
  (@thekid)

## 5.1.0 / 2020-09-09

* Merged PR #12: Improve tokenizer performance - @thekid
* Merged PR #11: Yield comments from tokenizer - @thekid
* Merged PR #10: Fix PHP 8 native syntax - @thekid

## 5.0.0 / 2020-07-20

This release integrates the parser into this library, making it usable
in a standalone way to create ASTs from string and/or stream inputs.
Now that the PHP project has decided on a final attribute syntax, this
library also supports it.

* Merged PR #8: Add support for match expression - @thekid
* Added builtin support for null-safe instance operator `?->`, see
  https://wiki.php.net/rfc/nullsafe_operator & xp-framework/compiler#9
  (@thekid)
* Merged PR #7: PHP8 attributes support - @thekid
* Integrated `throw` expressions, see xp-framework/compiler#85 - @thekid
* Fixed missing support for `insteadof` trait keyword - @thekid
* Merged PR #1: Integrate parser
  - Migrate tokenizer, parser and language from xp-framework/compiler
  - Added enclosing type to constants, properties and methods
  - Added `lang.ast.Node::is()` method to check for node kind
  - Added `lang.ast.nodes.TypeDeclaration::declare()` in favor of `inject`.
  - Added new parse tree API
  - Added new visitor API
  - Refactored types API to dedicated package `lang.ast.types`
  - Added dedicated classes for nullable, literal and value types
  (@thekid)

## 4.0.0 / 2019-11-30

This major release drops PHP 5 support - the minimum required PHP version
is now 7.0.0.

* Dropped support for PHP 5.6, see xp-framework/rfc#334 - @thekid

## 3.0.3 / 2019-11-30

* Added compatibility with XP 10, see xp-framework/rfc#333 - @thekid

## 3.0.2 / 2019-10-04

* Fixed issue #5: Call to a member function children() on null - @thekid

## 3.0.1 / 2019-09-22

* Fixed xp-framework/compiler#71: Call to a member function children()
  on array, which occured with `if`, `catch` and `switch` statements
  as well as for array literals.
  (@thekid)

## 3.0.0 / 2019-09-09

This major release changes the transformation API: Functions are now
passed the code generation process and the node they've registered for.

* **Heads up:** Changed transformation API - functions must accept a
  reference to the code generation process as their first parameter.
  (@thekid)
* **Heads up:** Added kind to `UnaryExpression` constructor which is
  either `prefix` or `suffix`, supporting `++$i` as well as `$i++`.
  (@thekid)
* Merged PR #4: Add Node class replacing Element and Value - @thekid

## 2.0.0 / 2019-09-06

This release overhauls the AST API greatly to make it more useable for
creating syntax trees programmatically.

* Merged PR #3: Allow multiple transformations per kind - @thekid
* **Heads up:** Removed `Symbol` and `Node` classes, which belong to the
  compiler internals now only
  (@thekid)
* Merged PR #2: Refactor AST. This makes programmatic AST creation far
  easier and improves on memory usage.
  (@thekid)

## 1.4.0 / 2019-08-10

* Made compatible with PHP 7.4 - refrain using `{}` for string offsets
  (@thekid)

## 1.3.0 / 2018-04-02

* Added `lang.ast.nodes.UsingStatement` in preparation for adding support
  for the `using` statement in xp-framework/compiler#33
  (@thekid)

## 1.2.0 / 2018-03-30

* Added type to class constants - @thekid

## 1.1.0 / 2018-03-29

* Added support for `mixed` type - @thekid

## 1.0.1 / 2018-03-29

* Fixed nullable value types being emitted incorrectly - @thekid

## 1.0.0 / 2017-11-05

This release forms the first as a separate library.

* Extracted from XP Compiler, see xp-framework/compiler#22 - @thekid
