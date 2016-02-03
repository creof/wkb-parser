# Change Log
All notable changes to this project will be documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
- Reader load() method to allow reusing a Reader instance.
- Parser parse() method to allow reusing a Parser instance.
- 3DZ, 3DM, and 4DZM support for all types.
- Support for CIRCULARSTRING type.
- Support for COMPOUNDCURVE type.
- Support for CURVEPOLYGON type.
- Support for MULTICURVE type.
- Support for MULTISURFACE type.
- Preliminary support for POLYHEDRALSURFACE type.

### Changed
- Major refactoring of Parser class.
- Nested types are now checked for permitted types (ie. only Points in MultiPoint, etc.)

## [2.0.0] - 2015-11-18
### Added
- Change base namespace to CrEOF\Geo\WKB to avoid class collision with other CrEOF packages.

## [1.0.1] - 2015-11-17
### Changed
- Replaced if/else statement with ternary operator in parseInput method of Reader.

## [1.0.0] - 2015-11-16
### Added
- Initial release.
