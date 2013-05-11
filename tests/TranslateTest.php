<?php

class Foo {
    function __toString() {
        return 'Foo';
    }
}

class TranslateTest extends PHPUnit_Framework_TestCase
{
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

    function testSchemaArray() {
        $schema = array(
            'ul' => 'complex li[]',
            'table' => 'complex tbody',
        );

        $xml = '<ul/>';
        $arr = XML_Builder::xmlToArray($xml, $schema);
        self::assertEquals(array('ul'=>array('li'=>array())), $arr);

        $xml = '<ul><li/></ul>';
        $arr = XML_Builder::xmlToArray($xml, $schema);
        self::assertEquals(array('ul'=>array('li'=>array(null))), $arr);

        $xml = '<ul><li/><li/></ul>';
        $arr = XML_Builder::xmlToArray($xml, $schema);
        self::assertEquals(array('ul'=>array('li'=>array(null,null))), $arr);

        $xml = '<table><thead/></table>';
        $arr = XML_Builder::xmlToArray($xml, $schema);
        self::assertEquals(array('table'=>array('thead'=>null,'tbody'=>null)), $arr);
    }

    function testFilteredVar() {
        $builder = xml_builder(array('class' => 'array', 'filter' => 'TranslateTest::stringify'));
        $builder->xmlElem('root')
            ->xmlText(array(1,2,3))
        ->xmlEnd();

        self::assertEquals('[1,2,3]', $builder->xmlArray['root']);
    }

    static function stringify($var) {
        if (is_array($var)) {
            return json_encode($var);
        }
        return $var;
    }
}
