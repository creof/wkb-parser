# Change Log
All notable changes to this project will be documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]
### Added
- Tests for empty geometry objects.

### Changed
- NaN coordinates are not returned in point value array, empty point value now array().
- Reader::readDouble() now deprecated and calls Reader::readFloat().
- Reader::readDoubles() now deprecated and calls Reader::readFloats().

### Removed

## [2.2.0] - 2016-05-03
### Added
- Added Tests namespace to Composer PSR-0 dev autoload.
- Added 'dimension' key to returned array containing object dimensions (Z, M, or ZM).
- Reader::getMachineByteOrder method to detect running platform endianness.
### Changed
- Parser property with Reader instance no longer static.
- Replaced sprintf function call in Reader::unpackInput() with string concatenation.
- Updated PHPUnit config to be compliant with XSD.
- Updated PHPUnit config to use Composer autoload.
- Updated documentation with new usage pattern.
- Type name in returned array now contains only base type without dimensions (Z, M, and ZM).
- Reader::readDouble() now checks running platform endianness before byte-swapping values instead of assuming little-endian.
### Removed
- Removed now unused TestInit

## [2.1.0] - 2016-02-18
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
