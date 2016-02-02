<?php
/**
 * Copyright (C) 2016 Derek J. Lambert
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

namespace CrEOF\Geo\WKB;

use CrEOF\Geo\WKB\Exception\UnexpectedValueException;

/**
 * Parser for WKB/EWKB spatial object data
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license http://dlambert.mit-license.org MIT
 */
class Parser
{
    const WKB_TYPE_POINT              = 1;
    const WKB_TYPE_LINESTRING         = 2;
    const WKB_TYPE_POLYGON            = 3;
    const WKB_TYPE_MULTIPOINT         = 4;
    const WKB_TYPE_MULTILINESTRING    = 5;
    const WKB_TYPE_MULTIPOLYGON       = 6;
    const WKB_TYPE_GEOMETRYCOLLECTION = 7;
    const WKB_TYPE_CIRCULARSTRING     = 8;
    const WKB_TYPE_COMPOUNDCURVE      = 9;
    const WKB_TYPE_CURVEPOLYGON       = 10;
    const WKB_TYPE_MULTICURVE         = 11;
    const WKB_TYPE_MULTISURFACE       = 12;
    const WKB_TYPE_POLYHEDRALSURFACE  = 15;
    const WKB_TYPE_TIN                = 16;
    const WKB_TYPE_TRIANGLE           = 17;

    const WKB_FLAG_SRID               = 0x20000000;
    const WKB_FLAG_M                  = 0x40000000;
    const WKB_FLAG_Z                  = 0x80000000;

    const TYPE_GEOMETRY           = 'Geometry';
    const TYPE_POINT              = 'Point';
    const TYPE_LINESTRING         = 'LineString';
    const TYPE_POLYGON            = 'Polygon';
    const TYPE_MULTIPOINT         = 'MultiPoint';
    const TYPE_MULTILINESTRING    = 'MultiLineString';
    const TYPE_MULTIPOLYGON       = 'MultiPolygon';
    const TYPE_GEOMETRYCOLLECTION = 'GeometryCollection';
    const TYPE_CIRCULARSTRING     = 'CircularString';

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $srid;

    /**
     * @var int
     */
    private $cords;

    /**
     * @var Reader
     */
    private static $reader;

    /**
     * @param string $input
     */
    public function __construct($input = null)
    {
        self::$reader = new Reader();

        if (null !== $input) {
            self::$reader->load($input);
        }
    }

    /**
     * Parse input data
     *
     * @param string $input
     *
     * @return array
     * @throws UnexpectedValueException
     */
    public function parse($input = null)
    {
        if (null !== $input) {
            self::$reader->load($input);
        }

        return $this->geometry();
    }

    /**
     * Parse geometry data
     *
     * @return array
     * @throws UnexpectedValueException
     */
    private function geometry()
    {
        $this->byteOrder();

        $this->type  = $this->readType();
        $this->cords = $this->getCoordCount($this->type);
        $this->srid  = null;

        if ($this->hasTypeFlag($this->type, self::WKB_FLAG_SRID)) {
            $this->srid = $this->readSrid();
        }

        $typeName = $this->getBaseTypeName($this->type);

        return array(
            'type'  => $this->getTypeName($this->type),
            'srid'  => $this->srid,
            'value' => $this->$typeName()
        );
    }

    /**
     * Check presence flags
     *
     * @param int $type
     * @param int $flag
     *
     * @return bool
     */
    private function hasTypeFlag($type, $flag)
    {
        return ($type & $flag) === $flag;
    }

    /**
     * Get name of data type
     *
     * @param int $type
     *
     * @return string
     * @throws UnexpectedValueException
     */
    private function getBaseTypeName($type)
    {
        switch ($type & 0xFFFF) {
            case (self::WKB_TYPE_POINT):
                $typeName = self::TYPE_POINT;
                break;
            case (self::WKB_TYPE_LINESTRING):
                $typeName = self::TYPE_LINESTRING;
                break;
            case (self::WKB_TYPE_POLYGON):
                $typeName = self::TYPE_POLYGON;
                break;
            case (self::WKB_TYPE_MULTIPOINT):
                $typeName = self::TYPE_MULTIPOINT;
                break;
            case (self::WKB_TYPE_MULTILINESTRING):
                $typeName = self::TYPE_MULTILINESTRING;
                break;
            case (self::WKB_TYPE_MULTIPOLYGON):
                $typeName = self::TYPE_MULTIPOLYGON;
                break;
            case (self::WKB_TYPE_GEOMETRYCOLLECTION):
                $typeName = self::TYPE_GEOMETRYCOLLECTION;
                break;
            case (self::WKB_TYPE_CIRCULARSTRING):
                $typeName = self::TYPE_CIRCULARSTRING;
                break;
            default:
                throw new UnexpectedValueException(sprintf('Unsupported WKB type "%s".', $this->type));
                break;
        }

        return strtoupper($typeName);
    }

    /**
     * @param $type
     *
     * @return string
     * @throws UnexpectedValueException
     */
    private function getTypeName($type)
    {
        $typeName = $this->getBaseTypeName($type);
        $suffix   = '';

        switch ($type & (self::WKB_FLAG_Z | self::WKB_FLAG_M)) {
            case (0):
                break;
            case (self::WKB_FLAG_Z):
                $suffix = ' Z';
                break;
            case (self::WKB_FLAG_M):
                $suffix = ' M';
                break;
            case (self::WKB_FLAG_Z | self::WKB_FLAG_M):
                $suffix = ' ZM';
                break;
        }

        return $typeName . $suffix;
    }
    /**
     * Parse data byte order
     *
     * @throws UnexpectedValueException
     */
    private function byteOrder()
    {
        return self::$reader->readByteOrder();
    }

    /**
     * Parse data type
     *
     * @throws UnexpectedValueException
     */
    private function readType()
    {
        return self::$reader->readLong();
    }

    /**
     * Parse SRID value
     *
     * @throws UnexpectedValueException
     */
    private function readSrid()
    {
        return self::$reader->readLong();
    }

    /**
     * @return int
     * @throws UnexpectedValueException
     */
    private function readCount()
    {
        return self::$reader->readLong();
    }

    /**
     * @param int $type
     *
     * @return int
     */
    private function getCoordCount($type)
    {
        return 2 + (int) $this->hasTypeFlag($type, self::WKB_FLAG_M) + (int) $this->hasTypeFlag($type, self::WKB_FLAG_Z);
    }
    /**
     * Parse POINT values
     *
     * @return float[]
     * @throws UnexpectedValueException
     */
    private function point()
    {
        return self::$reader->readDoubles($this->cords);
    }

    /**
     * Parse LINESTRING value
     *
     * @return array
     * @throws UnexpectedValueException
     */
    private function lineString()
    {
        return $this->readPoints($this->readCount());
    }

    /**
     * Parse CIRCULARSTRING value
     *
     * @return array
     * @throws UnexpectedValueException
     */
    private function circularString()
    {
        return $this->readPoints($this->readCount());
    }

    /**
     * Parse POLYGON value
     *
     * @return array[]
     */
    private function polygon()
    {
        return $this->readLinearRings($this->readCount());
    }

    /**
     * Parse MULTIPOINT value
     *
     * @return array[]
     */
    private function multiPoint()
    {
        return $this->readWKBPoints($this->readCount());
    }

    /**
     * Parse MULTILINESTRING value
     *
     * @return array[]
     */
    private function multiLineString()
    {
        return $this->readWKBLineStrings($this->readCount());
    }

    /**
     * Parse MULTIPOLYGON value
     *
     * @return array[]
     */
    private function multiPolygon()
    {
        return $this->readWKBPolygons($this->readCount());
    }

    /**
     * Parse GEOMETRYCOLLECTION value
     *
     * @return array[]
     */
    private function geometryCollection()
    {
        return $this->readWKBGeometries($this->readCount());
    }

    /**
     * @param int $count
     *
     * @return array
     * @throws UnexpectedValueException
     */
    private function readPoints($count)
    {
        $points = array();

        for ($i = 0; $i < $count; $i++) {
            $points[] = $this->point();
        }

        return $points;
    }

    /**
     * @param int $count
     *
     * @return array
     * @throws UnexpectedValueException
     */
    private function readLinearRings($count)
    {
        $rings = array();

        for ($i = 0; $i < $count; $i++) {
            $rings[] = $this->readPoints($this->readCount());
        }

        return $rings;
    }

    /**
     * @return array
     */
    private function readWKBPoints($count)
    {
        $values = array();

        for ($i = 0; $i < $count; $i++) {
            $this->byteOrder();
            $this->readType();

            $values[] = $this->point();
        }

        return $values;
    }

    /**
     * @return array
     */
    private function readWKBLineStrings($count)
    {
        $values = array();

        for ($i = 0; $i < $count; $i++) {
            $this->byteOrder();
            $this->readType();

            $values[] = $this->readPoints($this->readCount());
        }

        return $values;
    }

    /**
     * @return array
     */
    private function readWKBPolygons($count)
    {
        $values = array();

        for ($i = 0; $i < $count; $i++) {
            $this->byteOrder();
            $this->readType();

            $values[] = $this->readLinearRings($this->readCount());
        }

        return $values;
    }

    /**
     * @return array
     * @throws UnexpectedValueException
     */
    private function readWKBGeometries($count)
    {
        $values = array();

        for ($i = 0; $i < $count; $i++) {
            $this->byteOrder();

            $type     = $this->readType();
            $typeName = $this->getBaseTypeName($type);

            $values[] = array(
                'type'  => $typeName,
                'value' => $this->$typeName()
            );
        }

        return $values;
    }
}
