<?php
class XMLBuilderSerializeTest extends PHPUnit_Framework_TestCase
{
    function testSerialize() {
        $builder = xml_builder(array('class'=>'serialize'));
        $builder->root
            ->_markArray('hoge')
        ->_;

        self::assertEquals('a:1:{s:4:"hoge";a:0:{}}', (string)$builder);
    }

    function testDateTime() {
        $builder = xml_builder(array('class' => 'serialize'));
        $builder->time_(new DateTime);

        self::assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', (string)$builder);
    }
}
