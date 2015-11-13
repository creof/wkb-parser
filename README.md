# creof/wkb-parser

[![Build Status](https://travis-ci.org/creof/wkb-parser.svg?branch=master)](https://travis-ci.org/creof/wkb-parser)
[![Code Climate](https://codeclimate.com/github/creof/wkb-parser/badges/gpa.svg)](https://codeclimate.com/github/creof/wkb-parser)
[![Test Coverage](https://codeclimate.com/github/creof/wkb-parser/badges/coverage.svg)](https://codeclimate.com/github/creof/wkb-parser/coverage)

Parser library for WKB/EWKB spatial object data.

## Usage

Pass value to be parsed in the constructor, then call parse() on the created object.

```php
$parser = new Parser($input);
$value  = $parser->parse();
```

## Return

The parser will return an array with the keys ```srid```, ```type```, and ```value```.
- ```srid``` is the SRID if EWKT was passed in the constructor, null otherwise.
- ```type``` is the spatial object type.
- ```value``` will contain an array with 2 numeric values, or nested arrays containing these depending on the spatial object type.
