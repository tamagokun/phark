phark(1) -- a package manager for modern php
============================================

## SYNOPSIS

Phark is a package manager for recent versions of PHP (5.3.1+). It 
provides dependencies, versioning and installation for packages either 
system-wide or for an individual project.

In addition to managing packages locally, Phark allows publishing
of packages to <http://pharkphp.org> for easy consumption by other 
developers.

See `phark help` for a list of available commands.

## INTRODUCTION

The simplest use-case is installing a package. To install a package
use `phark install mypackage`. This package is now installed and 
available system-wide. 

For installing dependencies locally for a project, you can created
a `Pharkdep` file listing the dependencies and then run `phark deps`.
This will fix the dependencies for the project to just the ones 
you've listed to give you a re-producable environment.

To list the packages installed, use `phark list` for the present
project's packages, or `phark list -g` for global packages. See `phark help list`
for how this works.

## PACKAGE DEVELOPERS

To publish your package, you must have a project with a Pharkspec in it
describing the package. See `phark help pharkspec` for more details about
this.

Relevant commands are available in the following help topics:

* init
  Answer some questions, create a basic Pharkspec. See `phark help init`.
* publish
  Publish your current package to <http://pharkphp.org>. See `phark help publish`
* yank
  Made a mistake? Yank the package quickly. See `phark help yank`

## CONFIGURATION

At present there isn't any configuration for Phark. I'm sure there will be
eventually. To see what Phark has installed and where, checkout the output of
`phark env`.

## CONTRIBUTIONS AND BUGS

I'd love help, whether it's code, documentation, crituques or criticism.

If you find issues, report them:

* github: <http://github.com/lox/phark/issues>
* email: phark-dev@googlegroups.com

Providing the output of `phark env` will help us debug problems.

## HISTORY

See <http://github.com/lox/phark>.

## AUTHOR

Lachlan Donald :: lox :: @lox :: <lachlan@ljd.cc>

