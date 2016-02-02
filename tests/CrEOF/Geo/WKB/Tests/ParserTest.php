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
                'value' => '010200008002000000000000000000000000000000000000000000000000000040000000000000f03f000000000'
                    . '000f03f0000000000000840',
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
                'value' => '0080000002000000020000000000000000000000000000000040000000000000003ff00000000000003ff000000'
                    . '00000004008000000000000',
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
                'value' => '010200004002000000000000000000000000000000000000000000000000000040000000000000f03f000000000'
                    . '000f03f0000000000000840',
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
                'value' => '0040000002000000020000000000000000000000000000000040000000000000003ff00000000000003ff000000'
                    . '00000004008000000000000',
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
                'value' => '01020000c0020000000000000000000000000000000000000000000000000000400000000000000840000000000'
                    . '000f03f000000000000f03f00000000000010400000000000001440',
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
                'value' => '00c00000020000000200000000000000000000000000000000400000000000000040080000000000003ff000000'
                    . '00000003ff000000000000040100000000000004014000000000000',
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
                'value' => '01020000a0e610000002000000000000000000000000000000000000000000000000000040000000000000f03f0'
                    . '00000000000f03f0000000000000840',
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
                'value' => '00a0000002000010e6000000020000000000000000000000000000000040000000000000003ff00000000000003'
                    . 'ff00000000000004008000000000000',
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
                'value' => '0102000060e610000002000000000000000000000000000000000000000000000000000040000000000000f03f0'
                    . '00000000000f03f0000000000000840',
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
                'value' => '0060000002000010e6000000020000000000000000000000000000000040000000000000003ff00000000000003'
                    . 'ff00000000000004008000000000000',
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
                'value' => '01020000e0e61000000200000000000000000000000000000000000000000000000000004000000000000008400'
                    . '00000000000f03f000000000000f03f00000000000010400000000000001440',
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
                'value' => '00e0000002000010e60000000200000000000000000000000000000000400000000000000040080000000000003'
                    . 'ff00000000000003ff000000000000040100000000000004014000000000000',
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
                'value' => '0103000000010000000500000000000000000000000000000000000000000000000000244000000000000000000'
                    . '00000000000244000000000000024400000000000000000000000000000244000000000000000000000000000000000',
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
                'value' => '0000000003000000010000000500000000000000000000000000000000402400000000000000000000000000004'
                    . '02400000000000040240000000000000000000000000000402400000000000000000000000000000000000000000000',
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
                'value' => '0103000020E61000000100000005000000000000000000000000000000000000000000000000002440000000000'
                    . '000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000'
                    . '0000000',
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
                'value' => '0020000003000010E60000000100000005000000000000000000000000000000004024000000000000000000000'
                    . '000000040240000000000004024000000000000000000000000000040240000000000000000000000000000000000000'
                    . '0000000',
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
                'value' => '0103000000020000000500000000000000000000000000000000000000000000000000244000000000000000000'
                    . '000000000002440000000000000244000000000000000000000000000002440000000000000000000000000000000000'
                    . '5000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C40000000000'
                    . '0001C4000000000000014400000000000001C4000000000000014400000000000001440',
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
                'value' => '0000000003000000020000000500000000000000000000000000000000402400000000000000000000000000004'
                    . '024000000000000402400000000000000000000000000004024000000000000000000000000000000000000000000000'
                    . '000000540140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C00000'
                    . '00000004014000000000000401C00000000000040140000000000004014000000000000',
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
            'testParsingNDRMultiRingPolygonZValue' => array(
                'value' => '0103000080020000000500000000000000000000000000000000000000000000000000f03f00000000000024400'
                    . '000000000000000000000000000004000000000000024400000000000002440000000000000004000000000000000000'
                    . '000000000002440000000000000004000000000000000000000000000000000000000000000f03f05000000000000000'
                    . '000004000000000000000400000000000001440000000000000004000000000000014400000000000001040000000000'
                    . '000144000000000000014400000000000000840000000000000144000000000000000400000000000000840000000000'
                    . '000004000000000000000400000000000001440',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonZValue' => array(
                'value' => '00800000030000000200000005000000000000000000000000000000003ff000000000000040240000000000000'
                    . '000000000000000400000000000000040240000000000004024000000000000400000000000000000000000000000004'
                    . '0240000000000004000000000000000000000000000000000000000000000003ff000000000000000000005400000000'
                    . '000000040000000000000004014000000000000400000000000000040140000000000004010000000000000401400000'
                    . '000000040140000000000004008000000000000401400000000000040000000000000004008000000000000400000000'
                    . '000000040000000000000004014000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingNDRMultiRingPolygonMValue' => array(
                'value' => '0103000040020000000500000000000000000000000000000000000000000000000000f03f00000000000024400'
                    . '000000000000000000000000000004000000000000024400000000000002440000000000000004000000000000000000'
                    . '000000000002440000000000000004000000000000000000000000000000000000000000000f03f05000000000000000'
                    . '000004000000000000000400000000000001440000000000000004000000000000014400000000000001040000000000'
                    . '000144000000000000014400000000000000840000000000000144000000000000000400000000000000840000000000'
                    . '000004000000000000000400000000000001440',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonMValue' => array(
                'value' => '00400000030000000200000005000000000000000000000000000000003ff000000000000040240000000000000'
                    . '000000000000000400000000000000040240000000000004024000000000000400000000000000000000000000000004'
                    . '0240000000000004000000000000000000000000000000000000000000000003ff000000000000000000005400000000'
                    . '000000040000000000000004014000000000000400000000000000040140000000000004010000000000000401400000'
                    . '000000040140000000000004008000000000000401400000000000040000000000000004008000000000000400000000'
                    . '000000040000000000000004014000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingNDRMultiRingPolygonZMValue' => array(
                'value' => '01030000c0020000000500000000000000000000000000000000000000000000000000f03f000000000000f0bf0'
                    . '0000000000024400000000000000000000000000000004000000000000000c0000000000000244000000000000024400'
                    . '00000000000004000000000000000c000000000000000000000000000002440000000000000004000000000000010c00'
                    . '0000000000000000000000000000000000000000000f03f000000000000f0bf050000000000000000000040000000000'
                    . '000004000000000000014400000000000000000000000000000004000000000000014400000000000001040000000000'
                    . '000f03f00000000000014400000000000001440000000000000084000000000000000400000000000001440000000000'
                    . '00000400000000000000840000000000000f03f000000000000004000000000000000400000000000001440000000000'
                    . '0000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, -1),
                            array(10, 0, 2, -2),
                            array(10, 10, 2, -2),
                            array(0, 10, 2, -4),
                            array(0, 0, 1, -1)
                        ),
                        array(
                            array(2, 2, 5, 0),
                            array(2, 5, 4, 1),
                            array(5, 5, 3, 2),
                            array(5, 2, 3, 1),
                            array(2, 2, 5, 0)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonZMValue' => array(
                'value' => '00c00000030000000200000005000000000000000000000000000000003ff0000000000000bff00000000000004'
                    . '02400000000000000000000000000004000000000000000c000000000000000402400000000000040240000000000004'
                    . '000000000000000c000000000000000000000000000000040240000000000004000000000000000c0100000000000000'
                    . '00000000000000000000000000000003ff0000000000000bff0000000000000000000054000000000000000400000000'
                    . '0000000401400000000000000000000000000004000000000000000401400000000000040100000000000003ff000000'
                    . '000000040140000000000004014000000000000400800000000000040000000000000004014000000000000400000000'
                    . '000000040080000000000003ff0000000000000400000000000000040000000000000004014000000000000000000000'
                    . '0000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'POLYGON ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, -1),
                            array(10, 0, 2, -2),
                            array(10, 10, 2, -2),
                            array(0, 10, 2, -4),
                            array(0, 0, 1, -1)
                        ),
                        array(
                            array(2, 2, 5, 0),
                            array(2, 5, 4, 1),
                            array(5, 5, 3, 2),
                            array(5, 2, 3, 1),
                            array(2, 2, 5, 0)
                        )
                    )
                )
            ),
            'testParsingNDRMultiRingPolygonValueWithSrid' => array(
                'value' => '0103000020E61000000200000005000000000000000000000000000000000000000000000000002440000000000'
                    . '000000000000000000024400000000000002440000000000000000000000000000024400000000000000000000000000'
                    . '000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400'
                    . '000000000001C4000000000000014400000000000001C4000000000000014400000000000001440',
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
                'value' => '0020000003000010E60000000200000005000000000000000000000000000000004024000000000000000000000'
                    . '000000040240000000000004024000000000000000000000000000040240000000000000000000000000000000000000'
                    . '00000000000000540140000000000004014000000000000401C0000000000004014000000000000401C0000000000004'
                    . '01C0000000000004014000000000000401C00000000000040140000000000004014000000000000',
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
            'testParsingNDRMultiRingPolygonZValueWithSrid' => array(
                'value' => '01030000a0e6100000020000000500000000000000000000000000000000000000000000000000f03f000000000'
                    . '000244000000000000000000000000000000040000000000000244000000000000024400000000000000040000000000'
                    . '00000000000000000002440000000000000004000000000000000000000000000000000000000000000f03f050000000'
                    . '000000000000040000000000000004000000000000014400000000000000040000000000000144000000000000010400'
                    . '000000000001440000000000000144000000000000008400000000000001440000000000000004000000000000008400'
                    . '00000000000004000000000000000400000000000001440',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonZValueWithSrid' => array(
                'value' => '00a0000003000010e60000000200000005000000000000000000000000000000003ff0000000000000402400000'
                    . '000000000000000000000004000000000000000402400000000000040240000000000004000000000000000000000000'
                    . '000000040240000000000004000000000000000000000000000000000000000000000003ff0000000000000000000054'
                    . '000000000000000400000000000000040140000000000004000000000000000401400000000000040100000000000004'
                    . '014000000000000401400000000000040080000000000004014000000000000400000000000000040080000000000004'
                    . '00000000000000040000000000000004014000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingNDRMultiRingPolygonMValueWithSrid' => array(
                'value' => '0103000060e6100000020000000500000000000000000000000000000000000000000000000000f03f000000000'
                    . '000244000000000000000000000000000000040000000000000244000000000000024400000000000000040000000000'
                    . '00000000000000000002440000000000000004000000000000000000000000000000000000000000000f03f050000000'
                    . '000000000000040000000000000004000000000000014400000000000000040000000000000144000000000000010400'
                    . '000000000001440000000000000144000000000000008400000000000001440000000000000004000000000000008400'
                    . '00000000000004000000000000000400000000000001440',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonMValueWithSrid' => array(
                'value' => '0060000003000010e60000000200000005000000000000000000000000000000003ff0000000000000402400000'
                    . '000000000000000000000004000000000000000402400000000000040240000000000004000000000000000000000000'
                    . '000000040240000000000004000000000000000000000000000000000000000000000003ff0000000000000000000054'
                    . '000000000000000400000000000000040140000000000004000000000000000401400000000000040100000000000004'
                    . '014000000000000401400000000000040080000000000004014000000000000400000000000000040080000000000004'
                    . '00000000000000040000000000000004014000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(10, 0, 2),
                            array(10, 10, 2),
                            array(0, 10, 2),
                            array(0, 0, 1)
                        ),
                        array(
                            array(2, 2, 5),
                            array(2, 5, 4),
                            array(5, 5, 3),
                            array(5, 2, 3),
                            array(2, 2, 5)
                        )
                    )
                )
            ),
            'testParsingNDRMultiRingPolygonZMValueWithSrid' => array(
                'value' => '01030000e0e6100000020000000500000000000000000000000000000000000000000000000000f03f000000000'
                    . '000f0bf00000000000024400000000000000000000000000000004000000000000000c00000000000002440000000000'
                    . '0002440000000000000004000000000000000c0000000000000000000000000000024400000000000000040000000000'
                    . '00010c000000000000000000000000000000000000000000000f03f000000000000f0bf0500000000000000000000400'
                    . '000000000000040000000000000144000000000000000000000000000000040000000000000144000000000000010400'
                    . '00000000000f03f000000000000144000000000000014400000000000000840000000000000004000000000000014400'
                    . '0000000000000400000000000000840000000000000f03f0000000000000040000000000000004000000000000014400'
                    . '000000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, -1),
                            array(10, 0, 2, -2),
                            array(10, 10, 2, -2),
                            array(0, 10, 2, -4),
                            array(0, 0, 1, -1)
                        ),
                        array(
                            array(2, 2, 5, 0),
                            array(2, 5, 4, 1),
                            array(5, 5, 3, 2),
                            array(5, 2, 3, 1),
                            array(2, 2, 5, 0)
                        )
                    )
                )
            ),
            'testParsingXDRMultiRingPolygonZMValueWithSrid' => array(
                'value' => '00e0000003000010e60000000200000005000000000000000000000000000000003ff0000000000000bff000000'
                    . '0000000402400000000000000000000000000004000000000000000c0000000000000004024000000000000402400000'
                    . '00000004000000000000000c000000000000000000000000000000040240000000000004000000000000000c01000000'
                    . '0000000000000000000000000000000000000003ff0000000000000bff00000000000000000000540000000000000004'
                    . '000000000000000401400000000000000000000000000004000000000000000401400000000000040100000000000003'
                    . 'ff0000000000000401400000000000040140000000000004008000000000000400000000000000040140000000000004'
                    . '00000000000000040080000000000003ff00000000000004000000000000000400000000000000040140000000000000'
                    . '000000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'POLYGON ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, -1),
                            array(10, 0, 2, -2),
                            array(10, 10, 2, -2),
                            array(0, 10, 2, -4),
                            array(0, 0, 1, -1)
                        ),
                        array(
                            array(2, 2, 5, 0),
                            array(2, 5, 4, 1),
                            array(5, 5, 3, 2),
                            array(5, 2, 3, 1),
                            array(2, 2, 5, 0)
                        )
                    )
                )
            ),
            'testParsingNDRMultiPointValue' => array(
                'value' => '0104000000040000000101000000000000000000000000000000000000000101000000000000000000244000000'
                    . '00000000000010100000000000000000024400000000000002440010100000000000000000000000000000000002440',
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
                'value' => '0000000004000000040000000001000000000000000000000000000000000000000001402400000000000000000'
                    . '00000000000000000000140240000000000004024000000000000000000000100000000000000004024000000000000',
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
            'testParsingNDRMultiPointZValue' => array(
                'value' => '0104000080020000000101000080000000000000000000000000000000000000000000000000010100008000000'
                    . '000000000400000000000000000000000000000f03f',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT Z',
                    'value' => array(
                        array(0, 0, 0),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingXDRMultiPointZValue' => array(
                'value' => '0080000004000000020080000001000000000000000000000000000000000000000000000000008000000140000'
                    . '0000000000000000000000000003ff0000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT Z',
                    'value' => array(
                        array(0, 0, 0),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingNDRMultiPointMValue' => array(
                'value' => '0104000040020000000101000040000000000000000000000000000000000000000000000040010100004000000'
                    . '000000000400000000000000000000000000000f03f',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT M',
                    'value' => array(
                        array(0, 0, 2),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingXDRMultiPointMValue' => array(
                'value' => '0040000004000000020040000001000000000000000000000000000000004000000000000000004000000140000'
                    . '0000000000000000000000000003ff0000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT M',
                    'value' => array(
                        array(0, 0, 2),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingNDRMultiPointZMValue' => array(
                'value' => '01040000c00200000001010000c00000000000000000000000000000f03f0000000000000040000000000000084'
                    . '001010000c000000000000008400000000000000040000000000000f03f0000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT ZM',
                    'value' => array(
                        array(0, 1, 2, 3),
                        array(3, 2, 1, 0)
                    )
                )
            ),
            'testParsingXDRMultiPointZMValue' => array(
                'value' => '00c00000040000000200c000000100000000000000003ff00000000000004000000000000000400800000000000'
                    . '000c0000001400800000000000040000000000000003ff00000000000000000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT ZM',
                    'value' => array(
                        array(0, 1, 2, 3),
                        array(3, 2, 1, 0)
                    )
                )
            ),
            'testParsingNDRMultiPointValueWithSrid' => array(
                'value' => '0104000020E61000000400000001010000000000000000000000000000000000000001010000000000000000002'
                    . '440000000000000000001010000000000000000002440000000000000244001010000000000000000000000000000000'
                    . '0002440',
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
                'value' => '0020000004000010E60000000400000000010000000000000000000000000000000000000000014024000000000'
                    . '000000000000000000000000000014024000000000000402400000000000000000000010000000000000000402400000'
                    . '0000000',
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
            'testParsingNDRMultiPointZValueWithSrid' => array(
                'value' => '0104000080020000000101000080000000000000000000000000000000000000000000000000010100008000000'
                    . '000000000400000000000000000000000000000f03f',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT Z',
                    'value' => array(
                        array(0, 0, 0),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingXDRMultiPointZValueWithSrid' => array(
                'value' => '0080000004000000020080000001000000000000000000000000000000000000000000000000008000000140000'
                    . '0000000000000000000000000003ff0000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT Z',
                    'value' => array(
                        array(0, 0, 0),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingNDRMultiPointMValueWithSrid' => array(
                'value' => '0104000040020000000101000040000000000000000000000000000000000000000000000040010100004000000'
                    . '000000000400000000000000000000000000000f03f',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT M',
                    'value' => array(
                        array(0, 0, 2),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingXDRMultiPointMValueWithSrid' => array(
                'value' => '0040000004000000020040000001000000000000000000000000000000004000000000000000004000000140000'
                    . '0000000000000000000000000003ff0000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT M',
                    'value' => array(
                        array(0, 0, 2),
                        array(2, 0, 1)
                    )
                )
            ),
            'testParsingNDRMultiPointZMValueWithSrid' => array(
                'value' => '01040000c00200000001010000c00000000000000000000000000000f03f0000000000000040000000000000084'
                    . '001010000c000000000000008400000000000000040000000000000f03f0000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT ZM',
                    'value' => array(
                        array(0, 1, 2, 3),
                        array(3, 2, 1, 0)
                    )
                )
            ),
            'testParsingXDRMultiPointZMValueWithSrid' => array(
                'value' => '00c00000040000000200c000000100000000000000003ff00000000000004000000000000000400800000000000'
                    . '000c0000001400800000000000040000000000000003ff00000000000000000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOINT ZM',
                    'value' => array(
                        array(0, 1, 2, 3),
                        array(3, 2, 1, 0)
                    )
                )
            ),
            'testParsingNDRMultiLineStringValue' => array(
                'value' => '0105000000020000000102000000040000000000000000000000000000000000000000000000000024400000000'
                    . '000000000000000000000244000000000000024400000000000000000000000000000244001020000000400000000000'
                    . '0000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001C4000000'
                    . '000000014400000000000001C40',
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
                'value' => '0000000005000000020000000002000000040000000000000000000000000000000040240000000000000000000'
                    . '000000000402400000000000040240000000000000000000000000000402400000000000000000000020000000440140'
                    . '000000000004014000000000000401C0000000000004014000000000000401C000000000000401C00000000000040140'
                    . '00000000000401C000000000000',
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
            'testParsingNDRMultiLineStringZValue' => array(
                'value' => '01050000800200000001020000800200000000000000000000000000000000000000000000000000f03f0000000'
                    . '00000004000000000000000000000000000000040010200008002000000000000000000f03f000000000000f03f00000'
                    . '00000000840000000000000004000000000000000400000000000001040',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringZValue' => array(
                'value' => '008000000500000002008000000200000002000000000000000000000000000000003ff00000000000004000000'
                    . '000000000000000000000000040000000000000000080000002000000023ff00000000000003ff000000000000040080'
                    . '00000000000400000000000000040000000000000004010000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingNDRMultiLineStringMValue' => array(
                'value' => '01050000400200000001020000400200000000000000000000000000000000000000000000000000f03f0000000'
                    . '00000004000000000000000000000000000000040010200004002000000000000000000f03f000000000000f03f00000'
                    . '00000000840000000000000004000000000000000400000000000001040',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringMValue' => array(
                'value' => '004000000500000002004000000200000002000000000000000000000000000000003ff00000000000004000000'
                    . '000000000000000000000000040000000000000000040000002000000023ff00000000000003ff000000000000040080'
                    . '00000000000400000000000000040000000000000004010000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingNDRMultiLineStringZMValue' => array(
                'value' => '01050000c00200000001020000c00200000000000000000000000000000000000000000000000000f03f0000000'
                    . '000001440000000000000004000000000000000000000000000000040000000000000104001020000c00200000000000'
                    . '0000000f03f000000000000f03f000000000000084000000000000008400000000000000040000000000000004000000'
                    . '000000010400000000000000040',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, 5),
                            array(2, 0, 2, 4)
                        ),
                        array(
                            array(1, 1, 3, 3),
                            array(2, 2, 4, 2)
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringZMValue' => array(
                'value' => '00c00000050000000200c000000200000002000000000000000000000000000000003ff00000000000004014000'
                    . '000000000400000000000000000000000000000004000000000000000401000000000000000c0000002000000023ff00'
                    . '000000000003ff0000000000000400800000000000040080000000000004000000000000000400000000000000040100'
                    . '000000000004000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, 5),
                            array(2, 0, 2, 4)
                        ),
                        array(
                            array(1, 1, 3, 3),
                            array(2, 2, 4, 2)
                        )
                    )
                )
            ),
            'testParsingNDRMultiLineStringValueWithSrid' => array(
                'value' => '0105000020E61000000200000001020000000400000000000000000000000000000000000000000000000000244'
                    . '000000000000000000000000000002440000000000000244000000000000000000000000000002440010200000004000'
                    . '000000000000000144000000000000014400000000000001C4000000000000014400000000000001C400000000000001'
                    . 'C4000000000000014400000000000001C40',
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
                'value' => '0020000005000010E60000000200000000020000000400000000000000000000000000000000402400000000000'
                    . '000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000200000'
                    . '00440140000000000004014000000000000401C0000000000004014000000000000401C000000000000401C000000000'
                    . '0004014000000000000401C000000000000',
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
            'testParsingNDRMultiLineStringZValueWithSrid' => array(
                'value' => '01050000a0e61000000200000001020000800200000000000000000000000000000000000000000000000000f03'
                    . 'f000000000000004000000000000000000000000000000040010200008002000000000000000000f03f000000000000f'
                    . '03f0000000000000840000000000000004000000000000000400000000000001040',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTILINESTRING Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringZValueWithSrid' => array(
                'value' => '008000000500000002008000000200000002000000000000000000000000000000003ff00000000000004000000'
                    . '000000000000000000000000040000000000000000080000002000000023ff00000000000003ff000000000000040080'
                    . '00000000000400000000000000040000000000000004010000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING Z',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingNDRMultiLineStringMValueWithSrid' => array(
                'value' => '0105000060e61000000200000001020000400200000000000000000000000000000000000000000000000000f03'
                    . 'f000000000000004000000000000000000000000000000040010200004002000000000000000000f03f000000000000f'
                    . '03f0000000000000840000000000000004000000000000000400000000000001040',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTILINESTRING M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringMValueWithSrid' => array(
                'value' => '004000000500000002004000000200000002000000000000000000000000000000003ff00000000000004000000'
                    . '000000000000000000000000040000000000000000040000002000000023ff00000000000003ff000000000000040080'
                    . '00000000000400000000000000040000000000000004010000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING M',
                    'value' => array(
                        array(
                            array(0, 0, 1),
                            array(2, 0, 2)
                        ),
                        array(
                            array(1, 1, 3),
                            array(2, 2, 4)
                        )
                    )
                )
            ),
            'testParsingNDRMultiLineStringZMValueWithSrid' => array(
                'value' => '01050000e0e61000000200000001020000c00200000000000000000000000000000000000000000000000000f03'
                    . 'f0000000000001440000000000000004000000000000000000000000000000040000000000000104001020000c002000'
                    . '000000000000000f03f000000000000f03f0000000000000840000000000000084000000000000000400000000000000'
                    . '04000000000000010400000000000000040',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTILINESTRING ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, 5),
                            array(2, 0, 2, 4)
                        ),
                        array(
                            array(1, 1, 3, 3),
                            array(2, 2, 4, 2)
                        )
                    )
                )
            ),
            'testParsingXDRMultiLineStringZMValueWithSrid' => array(
                'value' => '00c00000050000000200c000000200000002000000000000000000000000000000003ff00000000000004014000'
                    . '000000000400000000000000000000000000000004000000000000000401000000000000000c0000002000000023ff00'
                    . '000000000003ff0000000000000400800000000000040080000000000004000000000000000400000000000000040100'
                    . '000000000004000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTILINESTRING ZM',
                    'value' => array(
                        array(
                            array(0, 0, 1, 5),
                            array(2, 0, 2, 4)
                        ),
                        array(
                            array(1, 1, 3, 3),
                            array(2, 2, 4, 2)
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonValue' => array(
                'value' => '0106000000020000000103000000020000000500000000000000000000000000000000000000000000000000244'
                    . '000000000000000000000000000002440000000000000244000000000000000000000000000002440000000000000000'
                    . '0000000000000000005000000000000000000144000000000000014400000000000001C4000000000000014400000000'
                    . '000001C400000000000001C4000000000000014400000000000001C40000000000000144000000000000014400103000'
                    . '0000100000005000000000000000000F03F000000000000F03F0000000000000840000000000000F03F0000000000000'
                    . '8400000000000000840000000000000F03F0000000000000840000000000000F03F000000000000F03F',
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
                'value' => '0000000006000000020000000003000000020000000500000000000000000000000000000000402400000000000'
                    . '000000000000000004024000000000000402400000000000000000000000000004024000000000000000000000000000'
                    . '000000000000000000000000540140000000000004014000000000000401C0000000000004014000000000000401C000'
                    . '000000000401C0000000000004014000000000000401C000000000000401400000000000040140000000000000000000'
                    . '00300000001000000053FF00000000000003FF000000000000040080000000000003FF00000000000004008000000000'
                    . '00040080000000000003FF000000000000040080000000000003FF00000000000003FF0000000000000',
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
            'testParsingNDRMultiPolygonZValue' => array(
                'value' => '0106000080010000000103000080020000000500000000000000000000000000000000000000000000000000084'
                    . '000000000000024400000000000000000000000000000084000000000000024400000000000002440000000000000084'
                    . '000000000000000000000000000002440000000000000084000000000000000000000000000000000000000000000084'
                    . '005000000000000000000004000000000000000400000000000000840000000000000004000000000000014400000000'
                    . '000000840000000000000144000000000000014400000000000000840000000000000144000000000000000400000000'
                    . '000000840000000000000004000000000000000400000000000000840',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON Z',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonZValue' => array(
                'value' => '0080000006000000010080000003000000020000000500000000000000000000000000000000400800000000000'
                    . '040240000000000000000000000000000400800000000000040240000000000004024000000000000400800000000000'
                    . '000000000000000004024000000000000400800000000000000000000000000000000000000000000400800000000000'
                    . '000000005400000000000000040000000000000004008000000000000400000000000000040140000000000004008000'
                    . '000000000401400000000000040140000000000004008000000000000401400000000000040000000000000004008000'
                    . '000000000400000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON Z',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonMValue' => array(
                'value' => '0106000040010000000103000040020000000500000000000000000000000000000000000000000000000000084'
                    . '000000000000024400000000000000000000000000000084000000000000024400000000000002440000000000000084'
                    . '000000000000000000000000000002440000000000000084000000000000000000000000000000000000000000000084'
                    . '005000000000000000000004000000000000000400000000000000840000000000000004000000000000014400000000'
                    . '000000840000000000000144000000000000014400000000000000840000000000000144000000000000000400000000'
                    . '000000840000000000000004000000000000000400000000000000840',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON M',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonMValue' => array(
                'value' => '0040000006000000010040000003000000020000000500000000000000000000000000000000400800000000000'
                    . '040240000000000000000000000000000400800000000000040240000000000004024000000000000400800000000000'
                    . '000000000000000004024000000000000400800000000000000000000000000000000000000000000400800000000000'
                    . '000000005400000000000000040000000000000004008000000000000400000000000000040140000000000004008000'
                    . '000000000401400000000000040140000000000004008000000000000401400000000000040000000000000004008000'
                    . '000000000400000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON M',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonZMValue' => array(
                'value' => '01060000c00100000001030000c0020000000500000000000000000000000000000000000000000000000000084'
                    . '000000000000000400000000000002440000000000000000000000000000008400000000000000040000000000000244'
                    . '000000000000024400000000000000840000000000000004000000000000000000000000000002440000000000000084'
                    . '000000000000000400000000000000000000000000000000000000000000008400000000000000040050000000000000'
                    . '000000040000000000000004000000000000008400000000000000040000000000000004000000000000014400000000'
                    . '000000840000000000000004000000000000014400000000000001440000000000000084000000000000000400000000'
                    . '000001440000000000000004000000000000008400000000000000040000000000000004000000000000000400000000'
                    . '0000008400000000000000040',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON ZM',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3, 2),
                                array(10, 0, 3, 2),
                                array(10, 10, 3, 2),
                                array(0, 10, 3, 2),
                                array(0, 0, 3, 2)
                            ),
                            array(
                                array(2, 2, 3, 2),
                                array(2, 5, 3, 2),
                                array(5, 5, 3, 2),
                                array(5, 2, 3, 2),
                                array(2, 2, 3, 2)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonZMValue' => array(
                'value' => '00c00000060000000100c0000003000000020000000500000000000000000000000000000000400800000000000'
                    . '040000000000000004024000000000000000000000000000040080000000000004000000000000000402400000000000'
                    . '040240000000000004008000000000000400000000000000000000000000000004024000000000000400800000000000'
                    . '040000000000000000000000000000000000000000000000040080000000000004000000000000000000000054000000'
                    . '000000000400000000000000040080000000000004000000000000000400000000000000040140000000000004008000'
                    . '000000000400000000000000040140000000000004014000000000000400800000000000040000000000000004014000'
                    . '000000000400000000000000040080000000000004000000000000000400000000000000040000000000000004008000'
                    . '0000000004000000000000000',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'MULTIPOLYGON ZM',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3, 2),
                                array(10, 0, 3, 2),
                                array(10, 10, 3, 2),
                                array(0, 10, 3, 2),
                                array(0, 0, 3, 2)
                            ),
                            array(
                                array(2, 2, 3, 2),
                                array(2, 5, 3, 2),
                                array(5, 5, 3, 2),
                                array(5, 2, 3, 2),
                                array(2, 2, 3, 2)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonValueWithSrid' => array(
                'value' => '0106000020E61000000200000001030000000200000005000000000000000000000000000000000000000000000'
                    . '000002440000000000000000000000000000024400000000000002440000000000000000000000000000024400000000'
                    . '000000000000000000000000005000000000000000000144000000000000014400000000000001C40000000000000144'
                    . '00000000000001C400000000000001C4000000000000014400000000000001C400000000000001440000000000000144'
                    . '001030000000100000005000000000000000000F03F000000000000F03F0000000000000840000000000000F03F00000'
                    . '000000008400000000000000840000000000000F03F0000000000000840000000000000F03F000000000000F03F',
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
                'value' => '0020000006000010E60000000200000000030000000200000005000000000000000000000000000000004024000'
                    . '000000000000000000000000040240000000000004024000000000000000000000000000040240000000000000000000'
                    . '00000000000000000000000000000000540140000000000004014000000000000401C000000000000401400000000000'
                    . '0401C000000000000401C0000000000004014000000000000401C0000000000004014000000000000401400000000000'
                    . '0000000000300000001000000053FF00000000000003FF000000000000040080000000000003FF000000000000040080'
                    . '0000000000040080000000000003FF000000000000040080000000000003FF00000000000003FF0000000000000',
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
            'testParsingNDRMultiPolygonZValueWithSrid' => array(
                'value' => '01060000a0e61000000100000001030000800200000005000000000000000000000000000000000000000000000'
                    . '000000840000000000000244000000000000000000000000000000840000000000000244000000000000024400000000'
                    . '000000840000000000000000000000000000024400000000000000840000000000000000000000000000000000000000'
                    . '000000840050000000000000000000040000000000000004000000000000008400000000000000040000000000000144'
                    . '000000000000008400000000000001440000000000000144000000000000008400000000000001440000000000000004'
                    . '00000000000000840000000000000004000000000000000400000000000000840',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON Z',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonZValueWithSrid' => array(
                'value' => '00a0000006000010e60000000100800000030000000200000005000000000000000000000000000000004008000'
                    . '000000000402400000000000000000000000000004008000000000000402400000000000040240000000000004008000'
                    . '000000000000000000000000040240000000000004008000000000000000000000000000000000000000000004008000'
                    . '000000000000000054000000000000000400000000000000040080000000000004000000000000000401400000000000'
                    . '040080000000000004014000000000000401400000000000040080000000000004014000000000000400000000000000'
                    . '04008000000000000400000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON Z',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonMValueWithSrid' => array(
                'value' => '0106000060e61000000100000001030000400200000005000000000000000000000000000000000000000000000'
                    . '000000840000000000000244000000000000000000000000000000840000000000000244000000000000024400000000'
                    . '000000840000000000000000000000000000024400000000000000840000000000000000000000000000000000000000'
                    . '000000840050000000000000000000040000000000000004000000000000008400000000000000040000000000000144'
                    . '000000000000008400000000000001440000000000000144000000000000008400000000000001440000000000000004'
                    . '00000000000000840000000000000004000000000000000400000000000000840',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON M',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonMValueWithSrid' => array(
                'value' => '0060000006000010e60000000100400000030000000200000005000000000000000000000000000000004008000'
                    . '000000000402400000000000000000000000000004008000000000000402400000000000040240000000000004008000'
                    . '000000000000000000000000040240000000000004008000000000000000000000000000000000000000000004008000'
                    . '000000000000000054000000000000000400000000000000040080000000000004000000000000000401400000000000'
                    . '040080000000000004014000000000000401400000000000040080000000000004014000000000000400000000000000'
                    . '04008000000000000400000000000000040000000000000004008000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON M',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3),
                                array(10, 0, 3),
                                array(10, 10, 3),
                                array(0, 10, 3),
                                array(0, 0, 3)
                            ),
                            array(
                                array(2, 2, 3),
                                array(2, 5, 3),
                                array(5, 5, 3),
                                array(5, 2, 3),
                                array(2, 2, 3)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRMultiPolygonZMValueWithSrid' => array(
                'value' => '01060000e0e61000000100000001030000c00200000005000000000000000000000000000000000000000000000'
                    . '000000840000000000000004000000000000024400000000000000000000000000000084000000000000000400000000'
                    . '000002440000000000000244000000000000008400000000000000040000000000000000000000000000024400000000'
                    . '000000840000000000000004000000000000000000000000000000000000000000000084000000000000000400500000'
                    . '000000000000000400000000000000040000000000000084000000000000000400000000000000040000000000000144'
                    . '000000000000008400000000000000040000000000000144000000000000014400000000000000840000000000000004'
                    . '000000000000014400000000000000040000000000000084000000000000000400000000000000040000000000000004'
                    . '000000000000008400000000000000040',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON ZM',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3, 2),
                                array(10, 0, 3, 2),
                                array(10, 10, 3, 2),
                                array(0, 10, 3, 2),
                                array(0, 0, 3, 2)
                            ),
                            array(
                                array(2, 2, 3, 2),
                                array(2, 5, 3, 2),
                                array(5, 5, 3, 2),
                                array(5, 2, 3, 2),
                                array(2, 2, 3, 2)
                            )
                        )
                    )
                )
            ),
            'testParsingXDRMultiPolygonZMValueWithSrid' => array(
                'value' => '00e0000006000010e60000000100c00000030000000200000005000000000000000000000000000000004008000'
                    . '000000000400000000000000040240000000000000000000000000000400800000000000040000000000000004024000'
                    . '000000000402400000000000040080000000000004000000000000000000000000000000040240000000000004008000'
                    . '000000000400000000000000000000000000000000000000000000000400800000000000040000000000000000000000'
                    . '540000000000000004000000000000000400800000000000040000000000000004000000000000000401400000000000'
                    . '040080000000000004000000000000000401400000000000040140000000000004008000000000000400000000000000'
                    . '040140000000000004000000000000000400800000000000040000000000000004000000000000000400000000000000'
                    . '040080000000000004000000000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'MULTIPOLYGON ZM',
                    'value' => array(
                        array(
                            array(
                                array(0, 0, 3, 2),
                                array(10, 0, 3, 2),
                                array(10, 10, 3, 2),
                                array(0, 10, 3, 2),
                                array(0, 0, 3, 2)
                            ),
                            array(
                                array(2, 2, 3, 2),
                                array(2, 5, 3, 2),
                                array(5, 5, 3, 2),
                                array(5, 2, 3, 2),
                                array(2, 2, 3, 2)
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionValue' => array(
                'value' => '01070000000300000001010000000000000000002440000000000000244001010000000000000000003E4000000'
                    . '00000003E400102000000020000000000000000002E400000000000002E4000000000000034400000000000003440',
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
                'value' => '0000000007000000030000000001402400000000000040240000000000000000000001403E000000000000403E0'
                    . '00000000000000000000200000002402E000000000000402E00000000000040340000000000004034000000000000',
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
            'testParsingNDRGeometryCollectionZValue' => array(
                'value' => '0107000080030000000101000080000000000000000000000000000000000000000000000000010200008002000'
                    . '000000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f000000000000f'
                    . '03f010700008002000000010100008000000000000000000000000000000000000000000000000001020000800200000'
                    . '0000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f000000000000f03'
                    . 'f',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION Z',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionZValue' => array(
                'value' => '0080000007000000030080000001000000000000000000000000000000000000000000000000008000000200000'
                    . '0020000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff0000000000'
                    . '000008000000700000002008000000100000000000000000000000000000000000000000000000000800000020000000'
                    . '20000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff000000000000'
                    . '0',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION Z',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionMValue' => array(
                'value' => '0107000040030000000101000040000000000000000000000000000000000000000000000000010200004002000'
                    . '000000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f000000000000f'
                    . '03f010700004002000000010100004000000000000000000000000000000000000000000000000001020000400200000'
                    . '0000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f000000000000f03'
                    . 'f',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION M',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionMValue' => array(
                'value' => '0040000007000000030040000001000000000000000000000000000000000000000000000000004000000200000'
                    . '0020000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff0000000000'
                    . '000004000000700000002004000000100000000000000000000000000000000000000000000000000400000020000000'
                    . '20000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff000000000000'
                    . '0',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION M',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionZMValue' => array(
                'value' => '01070000c00300000001010000c0000000000000000000000000000000000000000000000000000000000000f03'
                    . 'f01020000c0020000000000000000000000000000000000000000000000000000000000000000000040000000000000f'
                    . '03f000000000000f03f000000000000f03f000000000000084001070000c00200000001010000c000000000000000000'
                    . '0000000000000000000000000000000000000000000104001020000c0020000000000000000000000000000000000000'
                    . '000000000000000000000000000001440000000000000f03f000000000000f03f000000000000f03f000000000000184'
                    . '0',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION ZM',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0, 1)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0, 2),
                                array(1, 1, 1, 3)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0, 4)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0, 5),
                                        array(1, 1, 1, 6)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionZMValue' => array(
                'value' => '00c00000070000000300c00000010000000000000000000000000000000000000000000000003ff000000000000'
                    . '000c00000020000000200000000000000000000000000000000000000000000000040000000000000003ff0000000000'
                    . '0003ff00000000000003ff0000000000000400800000000000000c00000070000000200c000000100000000000000000'
                    . '0000000000000000000000000000000401000000000000000c0000002000000020000000000000000000000000000000'
                    . '0000000000000000040140000000000003ff00000000000003ff00000000000003ff0000000000000401800000000000'
                    . '0',
                'expected' => array(
                    'srid'  => null,
                    'type'  => 'GEOMETRYCOLLECTION ZM',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0, 1)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0, 2),
                                array(1, 1, 1, 3)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0, 4)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0, 5),
                                        array(1, 1, 1, 6)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionValueWithSrid' => array(
                'value' => '0107000020E61000000300000001010000000000000000002440000000000000244001010000000000000000003'
                    . 'E400000000000003E400102000000020000000000000000002E400000000000002E40000000000000344000000000000'
                    . '03440',
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
                'value' => '0020000007000010E6000000030000000001402400000000000040240000000000000000000001403E000000000'
                    . '000403E000000000000000000000200000002402E000000000000402E000000000000403400000000000040340000000'
                    . '00000',
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
            'testParsingNDRGeometryCollectionZValueWithSrid' => array(
                'value' => '01070000a0e61000000300000001010000800000000000000000000000000000000000000000000000000102000'
                    . '08002000000000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f00000'
                    . '0000000f03f0107000080020000000101000080000000000000000000000000000000000000000000000000010200008'
                    . '002000000000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f0000000'
                    . '00000f03f',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION Z',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionZValueWithSrid' => array(
                'value' => '00a0000007000010e60000000300800000010000000000000000000000000000000000000000000000000080000'
                    . '002000000020000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff00'
                    . '000000000000080000007000000020080000001000000000000000000000000000000000000000000000000008000000'
                    . '2000000020000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff0000'
                    . '000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION Z',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionMValueWithSrid' => array(
                'value' => '0107000060e61000000300000001010000400000000000000000000000000000000000000000000000000102000'
                    . '04002000000000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f00000'
                    . '0000000f03f0107000040020000000101000040000000000000000000000000000000000000000000000000010200004'
                    . '002000000000000000000000000000000000000000000000000000000000000000000f03f000000000000f03f0000000'
                    . '00000f03f',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION M',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionMValueWithSrid' => array(
                'value' => '0060000007000010e60000000300400000010000000000000000000000000000000000000000000000000040000'
                    . '002000000020000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff00'
                    . '000000000000040000007000000020040000001000000000000000000000000000000000000000000000000004000000'
                    . '2000000020000000000000000000000000000000000000000000000003ff00000000000003ff00000000000003ff0000'
                    . '000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION M',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0),
                                array(1, 1, 1)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0),
                                        array(1, 1, 1)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingNDRGeometryCollectionZMValueWithSrid' => array(
                'value' => '01070000e0e61000000300000001010000c00000000000000000000000000000000000000000000000000000000'
                    . '00000f03f01020000c002000000000000000000000000000000000000000000000000000000000000000000004000000'
                    . '0000000f03f000000000000f03f000000000000f03f000000000000084001070000c00200000001010000c0000000000'
                    . '000000000000000000000000000000000000000000000000000104001020000c00200000000000000000000000000000'
                    . '00000000000000000000000000000000000001440000000000000f03f000000000000f03f000000000000f03f0000000'
                    . '000001840',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION ZM',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0, 1)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0, 2),
                                array(1, 1, 1, 3)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0, 4)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0, 5),
                                        array(1, 1, 1, 6)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
            'testParsingXDRGeometryCollectionZMValueWithSrid' => array(
                'value' => '00e0000007000010e60000000300c00000010000000000000000000000000000000000000000000000003ff0000'
                    . '00000000000c00000020000000200000000000000000000000000000000000000000000000040000000000000003ff00'
                    . '000000000003ff00000000000003ff0000000000000400800000000000000c00000070000000200c0000001000000000'
                    . '000000000000000000000000000000000000000401000000000000000c00000020000000200000000000000000000000'
                    . '000000000000000000000000040140000000000003ff00000000000003ff00000000000003ff00000000000004018000'
                    . '000000000',
                'expected' => array(
                    'srid'  => 4326,
                    'type'  => 'GEOMETRYCOLLECTION ZM',
                    'value' => array(
                        array(
                            'type'  => 'POINT',
                            'value' => array(0, 0, 0, 1)
                        ),
                        array(
                            'type'  => 'LINESTRING',
                            'value' => array(
                                array(0, 0, 0, 2),
                                array(1, 1, 1, 3)
                            )
                        ),
                        array(
                            'type'  => 'GEOMETRYCOLLECTION',
                            'value' => array(
                                array(
                                    'type'  => 'POINT',
                                    'value' => array(0, 0, 0, 4)
                                ),
                                array(
                                    'type'  => 'LINESTRING',
                                    'value' => array(
                                        array(0, 0, 0, 5),
                                        array(1, 1, 1, 6)
                                    ),
                                )
                            )
                        )
                    )
                )
            ),
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
