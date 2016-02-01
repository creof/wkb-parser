<?php
/**
 * Copyright (C) 2015 Derek J. Lambert
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace CrEOF\Geo\WKB\Tests;

use CrEOF\Geo\WKB\Parser;

/**
 * Parser tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \CrEOF\Geo\WKB\Exception\UnexpectedValueException
     * @expectedExceptionMessage Invalid byte order "3"
     */
    public function testParsingBadByteOrder()
    {
        $value    = '03010000003D0AD7A3701D41400000000000C055C0';
        $value    = pack('H*', $value);
        $parser   = new Parser($value);

        $parser->parse();
    }

    /**
     * @expectedException        \CrEOF\Geo\WKB\Exception\UnexpectedValueException
     * @expectedExceptionMessage Unsupported WKB type "21"
     */
    public function testParsingBadType()
    {
        $value    = '01150000003D0AD7A3701D41400000000000C055C0';
        $value    = pack('H*', $value);
        $parser   = new Parser($value);

        $parser->parse();
    }

    /**
     * @expectedException              \PHPUnit_Framework_Error
     * @expectedExceptionMessageRegExp /Type d: not enough input, need 8, have 4$/
     */
    public function testParsingNDRShortPointValue()
    {
        $value    = '01010000003D0AD7A3701D414000000000';
        $value    = pack('H*', $value);
        $parser   = new Parser($value);
        $expected = array(
            'srid'  => null,
            'type'  => 'POINT',
            'value' => array(34.23, -87)
        );

        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function goodBinaryData()
    {
        return array(
            'testParsingNDRPointValue' => array(
                'value' => '01010000003D0AD7A3701D41400000000000C055C0',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POINT',
                    'value' => array(34.23, -87)
                )
            ),
            'testParsingXDRPointValue' => array(
                'value' => '000000000140411D70A3D70A3DC055C00000000000',
                'expected' => array(
                        'srid'  => null,
                        'type'  => 'POINT',
                        'value' => array(34.23, -87)
                )
            ),
            'testParsingNDRPointZValue' => array(
                'value' => '0101000080000000000000F03F00000000000000400000000000000840',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POINT Z',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingXDRPointZValue' => array(
                'value' => '00800000013FF000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POINT Z',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingNDRPointMValue' => array(
                'value' => '0101000040000000000000F03F00000000000000400000000000000840',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POINT M',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingXDRPointMValue' => array(
                'value' => '00400000013FF000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POINT M',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingNDRPointZMValue' => array(
                'value' => '01010000C0000000000000F03F000000000000004000000000000008400000000000001040',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POINT ZM',
                    'value' => array(1, 2, 3, 4)
                )
            ),
            'testParsingXDRPointZMValue' => array(
                'value' => '00C00000013FF0000000000000400000000000000040080000000000004010000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POINT ZM',
                    'value' => array(1, 2, 3, 4)
                )
            ),
            'testParsingNDRPointValueWithSrid' => array(
                'value' => '01010000003D0AD7A3701D41400000000000C055C0',
                'expected' => array(
                        'srid'  => null,
                        'type'  => 'POINT',
                        'value' => array(34.23, -87)
                    )
            ),
            'testParsingXDRPointValueWithSrid' => array(
                'value' => '0020000001000010E640411D70A3D70A3DC055C00000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POINT',
                    'value' => array(34.23, -87)
                )
            ),
            'testParsingNDRPointZValueWithSrid' => array(
                'value' => '01010000A0E6100000000000000000F03F00000000000000400000000000000840',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POINT Z',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingXDRPointZValueWithSrid' => array(
                'value' => '00A0000001000010E63FF000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POINT Z',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingNDRPointMValueWithSrid' => array(
                'value' => '0101000060e6100000000000000000f03f00000000000000400000000000000840',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POINT M',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingXDRPointMValueWithSrid' => array(
                'value' => '0060000001000010e63ff000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POINT M',
                    'value' => array(1, 2, 3)
                )
            ),
            'testParsingNDRPointZMValueWithSrid' => array(
                'value' => '01010000e0e6100000000000000000f03f000000000000004000000000000008400000000000001040',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POINT ZM',
                    'value' => array(1, 2, 3, 4)
                )
            ),
            'testParsingXDRPointZMValueWithSrid' => array(
                'value' => '00e0000001000010e63ff0000000000000400000000000000040080000000000004010000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POINT ZM',
                    'value' => array(1, 2, 3, 4)
                )
            ),
            'testParsingNDRLineStringValue' => array(
                'value' => '0102000000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING',
                    'value' => array(
                        array(34.23, -87),
                        array(45.3, -92)
                    )
                )
            ),
            'testParsingXDRLineStringValue' => array(
                'value' => '00000000020000000240411D70A3D70A3DC055C000000000004046A66666666666C057000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING',
                    'value' => array(
                        array(34.23, -87),
                        array(45.3, -92)
                    )
                )
            ),
            'testParsingNDRLineStringZValue' => array(
                'value' => '010200008002000000000000000000000000000000000000000000000000000040000000000000f03f000000000000f03f0000000000000840',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING Z',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingXDRLineStringZValue' => array(
                'value' => '0080000002000000020000000000000000000000000000000040000000000000003ff00000000000003ff00000000000004008000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING Z',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingNDRLineStringMValue' => array(
                'value' => '010200004002000000000000000000000000000000000000000000000000000040000000000000f03f000000000000f03f0000000000000840',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING M',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingXDRLineStringMValue' => array(
                'value' => '0040000002000000020000000000000000000000000000000040000000000000003ff00000000000003ff00000000000004008000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING M',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingNDRLineStringZMValue' => array(
                'value' => '01020000c0020000000000000000000000000000000000000000000000000000400000000000000840000000000000f03f000000000000f03f00000000000010400000000000001440',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING ZM',
                    'value' => array(
                        array(0, 0, 2, 3),
                        array(1, 1, 4, 5)
                    )
                )
            ),
            'testParsingXDRLineStringZMValue' => array(
                'value' => '00c00000020000000200000000000000000000000000000000400000000000000040080000000000003ff00000000000003ff000000000000040100000000000004014000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'LINESTRING ZM',
                    'value' => array(
                        array(0, 0, 2, 3),
                        array(1, 1, 4, 5)
                    )
                )
            ),
            'testParsingNDRLineStringValueWithSrid' => array(
                'value' => '0102000020E6100000020000003D0AD7A3701D41400000000000C055C06666666666A6464000000000000057C0',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING',
                    'value' => array(
                        array(34.23, -87),
                        array(45.3, -92)
                    )
                )
            ),
            'testParsingXDRLineStringValueWithSrid' => array(
                'value' => '0020000002000010E60000000240411D70A3D70A3DC055C000000000004046A66666666666C057000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING',
                    'value' => array(
                        array(34.23, -87),
                        array(45.3, -92)
                    )
                )
            ),
            'testParsingNDRLineStringZValueWithSrid' => array(
                'value' => '01020000a0e610000002000000000000000000000000000000000000000000000000000040000000000000f03f000000000000f03f0000000000000840',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING Z',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingXDRLineStringZValueWithSrid' => array(
                'value' => '00a0000002000010e6000000020000000000000000000000000000000040000000000000003ff00000000000003ff00000000000004008000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING Z',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingNDRLineStringMValueWithSrid' => array(
                'value' => '0102000060e610000002000000000000000000000000000000000000000000000000000040000000000000f03f000000000000f03f0000000000000840',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING M',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingXDRLineStringMValueWithSrid' => array(
                'value' => '0060000002000010e6000000020000000000000000000000000000000040000000000000003ff00000000000003ff00000000000004008000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING M',
                    'value' => array(
                        array(0, 0, 2),
                        array(1, 1, 3)
                    )
                )
            ),
            'testParsingNDRLineStringZMValueWithSrid' => array(
                'value' => '01020000e0e6100000020000000000000000000000000000000000000000000000000000400000000000000840000000000000f03f000000000000f03f00000000000010400000000000001440',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING ZM',
                    'value' => array(
                        array(0, 0, 2, 3),
                        array(1, 1, 4, 5)
                    )
                )
            ),
            'testParsingXDRLineStringZMValueWithSrid' => array(
                'value' => '00e0000002000010e60000000200000000000000000000000000000000400000000000000040080000000000003ff00000000000003ff000000000000040100000000000004014000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'LINESTRING ZM',
                    'value' => array(
                        array(0, 0, 2, 3),
                        array(1, 1, 4, 5)
                    )
                )
            ),
            'testParsingNDRPolygonValue' => array(
                'value' => '010300000001000000050000000000000000000000000000000000000000000000000024400000000000000000000000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        )
                    )
                )
            ),
            'testParsingXDRPolygonValue' => array(
                'value' => '000000000300000001000000050000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        )
                    )
                )
            ),
            'testParsingNDRPolygonValueWithSrid' => array(
                'value' => '0103000020E610000001000000050000000000000000000000000000000000000000000000000024400000000000000000000000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        )
                    )
                )
            ),
            'testParsingXDRPolygonValueWithSrid' => array(
                'value' => '0020000003000010E600000001000000050000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        )
                    )
                )
            ),
            'testParsingNDRMultiRingPolygonValue' => array(
                'value' => '01030000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C4000000000000014400000000000001440',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                            array(5, 5)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonValue' => array(
                'value' => '0000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                            array(5, 5)
                        )
                    )
                )
            ),
            'testParsingNDRMultiRingPolygonValueWithSrid' => array(
                'value' => '0103000020E61000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C4000000000000014400000000000001440',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                            array(5, 5)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonValueWithSrid' => array(
                'value' => '0020000003000010E6000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                            array(0, 0)
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                            array(5, 5)
                        )
                    )
                )
            ),
            'testParsingNDRMultiPointValue' => array(
                'value' => '010400000004000000010100000000000000000000000000000000000000010100000000000000000024400000000000000000010100000000000000000024400000000000002440010100000000000000000000000000000000002440',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT',
                    'value' => array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10)
                    )
                )
            ),
            'testParsingXDRMultiPointValue' => array(
                'value' => '000000000400000004000000000100000000000000000000000000000000000000000140240000000000000000000000000000000000000140240000000000004024000000000000000000000100000000000000004024000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT',
                    'value' => array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10)
                    )
                )
            ),
            'testParsingNDRMultiPointValueWithSrid' => array(
                'value' => '0104000020E610000004000000010100000000000000000000000000000000000000010100000000000000000024400000000000000000010100000000000000000024400000000000002440010100000000000000000000000000000000002440',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOINT',
                    'value' => array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10)
                    )
                )
            ),
            'testParsingXDRMultiPointValueWithSrid' => array(
                'value' => '0020000004000010E600000004000000000100000000000000000000000000000000000000000140240000000000000000000000000000000000000140240000000000004024000000000000000000000100000000000000004024000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOINT',
                    'value' => array(
                        array(0, 0),
                        array(10, 0),
                        array(10, 10),
                        array(0, 10)
                    )
                )
            ),
            'testParsingNDRMultiLineStringValue' => array(
                'value' => '01050000000200000001020000000400000000000000000000000000000000000000000000000000244000000000000000000000000000002440000000000000244000000000000000000000000000002440010200000004000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C40',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringValue' => array(
                'value' => '0000000005000000020000000002000000040000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000020000000440140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                        )
                    )
                )
            ),
            'testParsingNDRMultiLineStringValueWithSrid' => array(
                'value' => '0105000020E61000000200000001020000000400000000000000000000000000000000000000000000000000244000000000000000000000000000002440000000000000244000000000000000000000000000002440010200000004000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C40',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTILINESTRING',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringValueWithSrid' => array(
                'value' => '0020000005000010E6000000020000000002000000040000000000000000000000000000000040240000000000000000000000000000402400000000000040240000000000000000000000000000402400000000000000000000020000000440140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTILINESTRING',
                    'value' => array(
                        array(
                            array(0, 0),
                            array(10, 0),
                            array(10, 10),
                            array(0, 10),
                        ),
                        array(
                            array(5, 5),
                            array(7, 5),
                            array(7, 7),
                            array(5, 7),
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonValue' => array(
                'value' => '01060000000200000001030000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C400000000000001440000000000000144001030000000100000005000000000000000000F03F000000000000F03F0000000000000840000000000000F03F00000000000008400000000000000840000000000000F03F0000000000000840000000000000F03F000000000000F03F',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON',
                    'value' => array(
                        array(
                            array(
                                array(0, 0),
                                array(10, 0),
                                array(10, 10),
                                array(0, 10),
                                array(0, 0)
                            ),
                            array(
                                array(5, 5),
                                array(7, 5),
                                array(7, 7),
                                array(5, 7),
                                array(5, 5)
                            )
                        ),
                        array(
                            array(
                                array(1, 1),
                                array(3, 1),
                                array(3, 3),
                                array(1, 3),
                                array(1, 1)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonValue' => array(
                'value' => '0000000006000000020000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000000000000300000001000000053FF00000000000003FF000000000000040080000000000003FF0000000000000400800000000000040080000000000003FF000000000000040080000000000003FF00000000000003FF0000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON',
                    'value' => array(
                        array(
                            array(
                                array(0, 0),
                                array(10, 0),
                                array(10, 10),
                                array(0, 10),
                                array(0, 0)
                            ),
                            array(
                                array(5, 5),
                                array(7, 5),
                                array(7, 7),
                                array(5, 7),
                                array(5, 5)
                            )
                        ),
                        array(
                            array(
                                array(1, 1),
                                array(3, 1),
                                array(3, 3),
                                array(1, 3),
                                array(1, 1)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonValueWithSrid' => array(
                'value' => '0106000020E61000000200000001030000000200000005000000000000000000000000000000000000000000000000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000000000014400000000000001C400000000000001440000000000000144001030000000100000005000000000000000000F03F000000000000F03F0000000000000840000000000000F03F00000000000008400000000000000840000000000000F03F0000000000000840000000000000F03F000000000000F03F',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON',
                    'value' => array(
                        array(
                            array(
                                array(0, 0),
                                array(10, 0),
                                array(10, 10),
                                array(0, 10),
                                array(0, 0)
                            ),
                            array(
                                array(5, 5),
                                array(7, 5),
                                array(7, 7),
                                array(5, 7),
                                array(5, 5)
                            )
                        ),
                        array(
                            array(
                                array(1, 1),
                                array(3, 1),
                                array(3, 3),
                                array(1, 3),
                                array(1, 1)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonValueWithSrid' => array(
                'value' => '0020000006000010E6000000020000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C0000000000004014000000000000401C00000000000040140000000000004014000000000000000000000300000001000000053FF00000000000003FF000000000000040080000000000003FF0000000000000400800000000000040080000000000003FF000000000000040080000000000003FF00000000000003FF0000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON',
                    'value' => array(
                        array(
                            array(
                                array(0, 0),
                                array(10, 0),
                                array(10, 10),
                                array(0, 10),
                                array(0, 0)
                            ),
                            array(
                                array(5, 5),
                                array(7, 5),
                                array(7, 7),
                                array(5, 7),
                                array(5, 5)
                            )
                        ),
                        array(
                            array(
                                array(1, 1),
                                array(3, 1),
                                array(3, 3),
                                array(1, 3),
                                array(1, 1)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionValue' => array(
                'value' => '01070000000300000001010000000000000000002440000000000000244001010000000000000000003E400000000000003E400102000000020000000000000000002E400000000000002E4000000000000034400000000000003440',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(10, 10)
                        ),
                        array(
                            'type'  => 'POINT',
                            'value' => array(30, 30)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(15, 15),
                                array(20, 20)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionValue' => array(
                'value' => '0000000007000000030000000001402400000000000040240000000000000000000001403E000000000000403E000000000000000000000200000002402E000000000000402E00000000000040340000000000004034000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(10, 10)
                        ),
                        array(
                            'type'  => 'POINT',
                            'value' => array(30, 30)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(15, 15),
                                array(20, 20)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionValueWithSrid' => array(
                'value' => '0107000020E61000000300000001010000000000000000002440000000000000244001010000000000000000003E400000000000003E400102000000020000000000000000002E400000000000002E4000000000000034400000000000003440',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(10, 10)
                        ),
                        array(
                            'type'  => 'POINT',
                            'value' => array(30, 30)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(15, 15),
                                array(20, 20)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionValueWithSrid' => array(
                'value' => '0020000007000010E6000000030000000001402400000000000040240000000000000000000001403E000000000000403E000000000000000000000200000002402E000000000000402E00000000000040340000000000004034000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(10, 10)
                        ),
                        array(
                            'type'  => 'POINT',
                            'value' => array(30, 30)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(15, 15),
                                array(20, 20)
                            )
                        )
                    )
                )
            )
        );
    }

    /**
     * @param       $value
     * @param array $expected
     *
     * @dataProvider goodBinaryData
     */
    public function testParser($value, array $expected)
    {
        $value  = pack('H*', $value);
        $parser = new Parser($value);
        $actual = $parser->parse();

        $this->assertEquals($expected, $actual);
    }

    /**
     */
    public function testReusedParser()
    {
        $parser = new Parser();

        foreach ($this->goodBinaryData() as $testData) {
            $value  = pack('H*', $testData['value']);
            $actual = $parser->parse($value);

            $this->assertEquals($testData['expected'], $actual);
        }
    }
}
