XP AST ChangeLog
================

## ?.?.? / ????-??-??

* Fixed xp-framework/compiler#71: Call to a member function children()
  on array, which occured with `if` statements
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
