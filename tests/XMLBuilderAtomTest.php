<?php
class XMLBuilderAtomTest extends PHPUnit_Framework_TestCase
{
    function testDateTime() {
        $builder = xml_builder(array('class'=>'atom'));
        $builder->root
            ->time_(new DateTime)
            ->times_(array('hoge'=>new DateTime, 'foo'=>new DateTime))
        ->_;

        self::assertRegExp('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', (string)$builder);
    }

    function testBool() {
        $builder = xml_builder(array('class'=>'atom'));
        $builder->root
            ->isOk_(true)
            ->someText_('some text')
        ->_;

        self::assertRegExp('/true/', (string)$builder);
    }
}
