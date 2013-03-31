<?php
class XMLBuilderXMLWriterTest extends PHPUnit_Framework_TestCase
{
    function testRaw() {
        $builder = xml_builder(array(
            'class' => 'xmlwriter',
            'formatOutput' => false
        ));

        $builder->root
            ->_raw('<hoge>fuga</hoge>')
            ->_;

        $expect = <<<_XML_
<?xml version="1.0" encoding="UTF-8"?>
<root><hoge>fuga</hoge></root>
_XML_;

        self::assertEquals($expect, (string)$builder);
    }

    function testWriteTo()
    {
        $builder = xml_builder(array(
            'class' => 'xmlwriter',
            'writeto' => 'writeto.xml',
        ));

        ob_start();
        $builder->_echo;
        $buf = ob_get_clean();
        self::assertEquals('', $buf);
    }
}
