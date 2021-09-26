XP AST ChangeLog
================

## ?.?.? / ????-??-??

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

* **Heads up:** Changed transformation API - functions must accept a
  reference to the code generation process as their first parameter.
  (@thekid)
* **Heads up:** Added kind to `UnaryExpression` constructor which is
  either `prefix` or `suffix`, supporting `++$i` as well as `$i++`.
  (@thekid)
* Merged PR #4: Add Node class replacing Element and Value - @thekid

## 2.0.0 / 2019-09-06

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

* Extracted from XP Compiler, see xp-framework/compiler#22 - @thekid
