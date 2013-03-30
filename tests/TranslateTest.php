<?php

class Foo {
    function __toString() {
        return 'Foo';
    }
}

class TranslateTest extends PHPUnit_Framework_TestCase
{
    function testToXsdType() {
        self::assertEquals('true', XML_Builder::toXsdType(true));
        self::assertEquals('false', XML_Builder::toXsdType(false));
        self::assertEquals(1, XML_Builder::toXsdType(1));

        $date = new DateTime('2012-01-01T00:00:00+09:00');
        $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
        self::assertEquals('2012-01-01T00:00:00+09:00', XML_Builder::toXsdType($date));
    }

    function testToJsonType() {
        self::assertEquals(true, XML_Builder::toJsonType(true));
        self::assertEquals(1, XML_Builder::toJsonType(1));
        self::assertEquals('Foo', XML_Builder::toJsonType(new Foo));

        $date = new DateTime('2012-01-01T00:00:00+09:00');
        $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
        self::assertEquals('2012-01-01T00:00:00+09:00', XML_Builder::toJsonType($date));
    }

    function testToJson() {
        self::assertEquals('[1,2,3]', XML_Builder::json(array('root' => array(1,2,3))));
    }
}
