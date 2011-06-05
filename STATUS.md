Development Status
==================

Phark is still under active development and is a fair way off from being usable. The
basic aim is to get a functional beta released that allows for the basics. Look at the list
below to see what's working and what need's doing. 

What's working?
---------------

* System installation
* Parsing of Pharkspec files
* Basic dependency resolution
* Local package installation. `phark install <directory>`.
* Remote package installation. `phark install <packagename>`.
* Listing global packages and project packages `phark list`
* Removing global packages with `phark remove`
* Show working environment with `phark environment`
* Installing of package dependencies `phark deps`, only with Pharkspec
* Basic bundling
* Basic documentation. `phark help` and `phark help <topic>`

What needs doing?
-----------------

* Fetching only with `phark fetch <package>`
* Better console output with pretty ANSI goodness
* Parsing Pharkdeps files, installing project deps. `phark deps`
* Checking PHP version
* Lots more documentation

What's left for the future
--------------------------

* Package signing, web of trust
* Web app for pharkphp.org (signup, register cert, accept package)
* Submitting phark files to pharkphp.org. `phark publish`
* Searching remote specifications
* Support grouped dependencies (developent deps only, for instance)
* GIT support in Pharkdeps
* SVN support in Pharkdeps
* PEAR support in Pharkdeps
* PECL support for building extensions
* Dependency locking. `Pharkdeps.lock`
* Windows support
