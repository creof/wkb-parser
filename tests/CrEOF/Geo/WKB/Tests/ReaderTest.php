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

use CrEOF\Geo\WKB\Reader;

/**
 * Reader tests
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    public function testReadingBinaryByteOrder()
    {
        $value  = '01';
        $value  = pack('H*', $value);
        $reader = new Reader($value);
        $result = $reader->readByteOrder();

        $this->assertEquals(1, $result);
    }

    public function testReadingHexByteOrder()
    {
        $value  = '01';
        $reader = new Reader($value);
        $result = $reader->readByteOrder();

        $this->assertEquals(1, $result);
    }

    public function testReadingPrefixedHexByteOrder()
    {
        $value  = '0x01';
        $reader = new Reader($value);
        $result = $reader->readByteOrder();

        $this->assertEquals(1, $result);
    }

    /**
     * @expectedException        \CrEOF\Geo\WKB\Exception\UnexpectedValueException
     * @expectedExceptionMessage Invalid byte order "unset"
     */
    public function testReadingBinaryWithoutByteOrder()
    {
        $value  = '0101000000';
        $value  = pack('H*', $value);
        $reader = new Reader($value);

        $reader->readLong();
    }

    /**
     * @expectedException        \CrEOF\Geo\WKB\Exception\UnexpectedValueException
     * @expectedExceptionMessage Invalid byte order "unset"
     */
    public function testReadingHexWithoutByteOrder()
    {
        $value  = '0101000000';
        $reader = new Reader($value);

        $reader->readLong();
    }

    public function testReadingNDRBinaryLong()
    {
        $value  = '0101000000';
        $value  = pack('H*', $value);
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readLong();

        $this->assertEquals(1, $result);
    }

    public function testReadingXDRBinaryLong()
    {
        $value  = '0000000001';
        $value  = pack('H*', $value);
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readLong();

        $this->assertEquals(1, $result);
    }

    public function testReadingNDRHexLong()
    {
        $value  = '0101000000';
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readLong();

        $this->assertEquals(1, $result);
    }

    public function testReadingXDRHexLong()
    {
        $value  = '0000000001';
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readLong();

        $this->assertEquals(1, $result);
    }

    public function testReadingNDRBinaryFloat()
    {
        $value  = '013D0AD7A3701D4140';
        $value  = pack('H*', $value);
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readFloat();

        $this->assertEquals(34.23, $result);
    }

    public function testReadingXDRBinaryFloat()
    {
        $value  = '0040411D70A3D70A3D';
        $value  = pack('H*', $value);
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readFloat();

        $this->assertEquals(34.23, $result);
    }

    public function testReadingNDRHexFloat()
    {
        $value  = '013D0AD7A3701D4140';
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readFloat();

        $this->assertEquals(34.23, $result);
    }

    public function testReadingXDRHexFloat()
    {
        $value  = '0040411D70A3D70A3D';
        $reader = new Reader($value);

        $reader->readByteOrder();

        $result = $reader->readFloat();

        $this->assertEquals(34.23, $result);
    }

    public function testReaderReuse()
    {
        $reader = new Reader();

        $value  = '01';
        $value  = pack('H*', $value);

        $reader->load($value);

        $result = $reader->readByteOrder();

        $this->assertEquals(1, $result);

        $value  = '01';

        $reader->load($value);

        $result = $reader->readByteOrder();

        $this->assertEquals(1, $result);

        $value  = '0x01';

        $reader->load($value);

        $result = $reader->readByteOrder();

        $this->assertEquals(1, $result);

        $value  = '0040411D70A3D70A3D';
        $value  = pack('H*', $value);

        $reader->load($value);

        $reader->readByteOrder();

        $result = $reader->readFloat();

        $this->assertEquals(34.23, $result);
    }
}
