XP AST ChangeLog
================

## ?.?.? / ????-??-??

## 1.4.0 / 2018-06-09

* Added comment member to `lang.ast.Node`.This will make it possible
  to attach comments to nodes; which in turn is a crucial part of
  reconstructing the code from the parse tree
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
