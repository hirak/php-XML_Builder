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

    function testJson() {
        self::assertEquals('[1,2,3]', XML_Builder::json(array('root' => array(1,2,3))));
    }

    /**
     * @requires PHP 5.4
     */
    function testJsonDebug() {
        self::assertEquals('true', XML_Builder::jsonDebug(array('root' => true)));
    }

    function testSerialize() {
        self::assertEquals('b:1;', XML_Builder::serialize(array('root' => true)));
    }

    /**
     * @requires extension yaml
     */
    function testYaml() {
        $generated = XML_Builder::yaml(array(
            'root' => array(
                'str'=>'string',
                'bool' => true
            )
        ));
        $expect = <<<_YAML_
---
str: string
bool: true
...

_YAML_;
        self::assertEquals($expect, $generated);
    }
}
