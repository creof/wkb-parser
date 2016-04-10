# creof/wkb-parser

[![Build Status](https://travis-ci.org/creof/wkb-parser.svg?branch=master)](https://travis-ci.org/creof/wkb-parser)
[![Code Climate](https://codeclimate.com/github/creof/wkb-parser/badges/gpa.svg)](https://codeclimate.com/github/creof/wkb-parser)
[![Test Coverage](https://codeclimate.com/github/creof/wkb-parser/badges/coverage.svg)](https://codeclimate.com/github/creof/wkb-parser/coverage)

Parser library for WKB/EWKB spatial object data.

## Usage

There are two use patterns for the parser. The value to be parsed can be passed into the constructor, then parse()
called on the returned ```Parser``` object:

```php
$parser = new Parser($input);

$value = $parser->parse();
```

If many values need to be parsed, a single ```Parser``` instance can be used:

```php
$parser = new Parser();

$value1 = $parser->parse($input1);
$value2 = $parser->parse($input2);
```

## Return

The parser will return an array with the keys ```type```, ```value```, and ```srid```.
- ```type``` is the spatial object type (POINT, LINESTRING, etc.)
- ```value``` will contain an array with integer or float values for points, or nested arrays containing these based on spatial object type.
- ```srid``` is the SRID if EWKT value was parsed, null otherwise.

## Exceptions

The ```Reader``` and ```Parser``` will throw exceptions implementing interface ```CrEOF\Geo\WKB\Exception\ExceptionInterface```.
