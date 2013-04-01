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

    function testYaml() {
        //PHPUnit < 3.7では@requiresが効かない
        if (! extension_loaded('yaml')) {
            return $this->markTestSkipped();
        }
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

    function testXmlToArray()
    {
        $xml = <<<_XML_
<?xml version="1.0" encoding="UTF-8"?>
<root xmlns:h="urn:example">
  <hoge>foo</hoge>
  <empty/>
  <!-- comment -->
  <fuga h:test="foo">123</fuga>
  <fuga><![CDATA[456]]></fuga>
</root>
_XML_;
        $expect = '{"root":{"@xmlns:h":"urn:example","hoge":"foo","empty":null,"fuga":[{"@h:test":"foo","$":"123"},"456"]}}';

        $arr = XML_Builder::xmlToArray($xml);

        self::assertEquals($expect, json_encode($arr));

        $dom = new DOMDocument;
        $dom->loadXML($xml, LIBXML_NOBLANKS);

        $arr = XML_Builder::domToArray($dom);
        self::assertEquals($expect, json_encode($arr));
    }

    function testDateTime() {
        $xml = '<date>2012-01-01T00:00:00</date>';

        $arr = XML_Builder::xmlToArray($xml, array('date'=>'dateTime'));
        self::assertInstanceOf('DateTime', $arr['date']);
    }

    function testSchema() {
        $xml = file_get_contents('tests/samplefeed.atom');

        $arr = XML_Builder::xmlToArray($xml, 'tests/schema.ini');
        self::assertContains('entry', current($arr));

        $xml = file_get_contents('tests/sample.xml');
        $arr = XML_Builder::xmlToArray($xml, 'tests/schema.ini');

        self::assertTrue(is_int($arr['moge']['@id']));
        self::assertTrue(is_string($arr['moge']['$']));

        $xml = file_get_contents('tests/samplefeed2.atom');
        $arr = XML_Builder::xmlToArray($xml, 'tests/schema.ini');
    }
}
