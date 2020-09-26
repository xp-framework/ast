XP AST ChangeLog
================

## ?.?.? / ????-??-??

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
