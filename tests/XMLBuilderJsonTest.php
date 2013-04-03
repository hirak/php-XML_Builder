<?php
class XMLBuilderJsonTest extends PHPUnit_Framework_TestCase
{
    function testDateTime() {
        $builder = xml_builder(array('class'=>'XML_Builder_Json'));
        $builder->root
            ->time_(new DateTime)
            ->times_(array('hoge'=>new DateTime, 'foo'=>new DateTime))
        ->_;

        self::assertTrue(is_string($builder->xmlArray['root']['time']));
        self::assertTrue(is_string($builder->xmlArray['root']['times']['@hoge']));
        self::assertTrue(is_string($builder->xmlArray['root']['times']['@foo']));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testJsonpCallback() {
        $builder = xml_builder(array(
            'class'=>'XML_Builder_Jsonp',
            'callback'=>'     ',
        ));
    }

    function testJsonp() {
        $builder = xml_builder(array(
            'class' => 'XML_Builder_Jsonp',
            'callback' => 'sample.call.back[0]'
        ));

        $builder->foo->hoge_->_;

        self::assertTrue(0 === strpos($builder->_render(), 'sample.call.back[0]'));
    }
}
