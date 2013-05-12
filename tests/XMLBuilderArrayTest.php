<?php
/**
 * XML_Builder_Arrayに限定したテスト
 *
 */

class XMLBuilderArrayTest extends PHPUnit_Framework_TestCase
{
    function testRender() {
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode'
        ));
        $builder->root->hogehoge_->_;

        self::assertEquals('{"hogehoge":null}', $builder->_render());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testRender2() {
        $builder = xml_builder(array(
            'class' => 'array',
            'removeRootNode' => false,
            'serializer' => 'uso800',
        ));

        $builder->root->hoge_->_->_render();
    }

    function testRaw() {
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));

        $builder->root->_raw('<hoge>fuga</hoge>')->_;

        self::assertEquals('"<hoge>fuga<\/hoge>"', (string)$builder);
    }

    function testRootNode() {
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
            'removeRootNode' => false,
        ));
        $builder->root_('text');

        self::assertEquals('{"root":"text"}', (string)$builder);
    }

    function testMarkArray() {
        //markArrayの基本機能 + 属性
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));
        $builder
            ->root(array('foo'=>'foo'))
                ->_markArray('foo')
            ->_;
        self::assertEquals('{"@foo":"foo","foo":[]}', (string)$builder);

        //contextが配列になっている && markはまだ存在しないとき
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));
        $builder
            ->root
                ->hoge_
                ->fuga_
                ->_markArray('foo')
            ->_;
        self::assertEquals('{"hoge":null,"fuga":null,"foo":[]}', (string)$builder);

        //contextが配列になっている && markが既に存在するとき
        // ->どうしようもないので、markを無視する
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));
        $builder
            ->root
                ->foo_
                ->moo_
                ->_markArray('foo')
                ->foo_
            ->_;
        self::assertEquals('{"$":[{"foo":null},{"moo":null},{"foo":null}]}', (string)$builder);

        //contextが配列になっている && markが既に配列として存在するとき
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));
        $builder
            ->root
                ->foo_
                ->foo_
                ->_markArray('foo')
            ->_;
        self::assertEquals('{"foo":[null,null]}', (string)$builder);

        //contextが配列になっている && markが既に存在するが配列でないとき
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));
        $builder
            ->root
                ->foo_
                ->_markArray('foo')
            ->_;
        self::assertEquals('{"foo":[null]}', (string)$builder);

        //contextが配列になっている && markが既に存在するが配列でないとき
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));
        $builder
            ->root
                ->foo
                    ->hoge_
                    ->fuga_
                ->_
                ->_markArray('foo')
            ->_;
        self::assertEquals('{"foo":[{"hoge":null,"fuga":null}]}', (string)$builder);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testMarkArray2() {
        //mixed contentはmarkArrayにできない
        $builder = xml_builder(array(
            'class' => 'array',
            'serializer' => 'json_encode',
        ));

        $builder
            ->root
                ->_text('str')
                ->foo_
                ->_markArray('foo')
            ->_;
    }
}
