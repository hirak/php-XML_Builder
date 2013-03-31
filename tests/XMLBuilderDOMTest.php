<?php
class XMLBuilderDOMTest extends PHPUnit_Framework_TestCase {
    function testRaw() {
        $builder = xml_builder(array(
            'class' => 'dom',
            'formatOutput' => false,
        ));

        $builder->root
            ->_raw('<hoge>foo</hoge>')
            ->_;

        $expect = <<<_XML_
<?xml version="1.0" encoding="UTF-8"?>
<root><hoge>foo</hoge></root>

_XML_;
        self::assertEquals($expect, (string)$builder);
    }
}
