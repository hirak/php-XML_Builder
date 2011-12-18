#!/usr/bin/env phpunit --colors
<?php
/**
 * テストの起動スクリプト
 * ひたすらtest*.phpとtest*.xmlの比較を行います。
 * PHPUnitが必要です。
 *
 */

require_once dirname(__FILE__) . '/../lib/XML/Builder.php';
class XML_BuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * DOM版で全テストケースを起動
     *
     * @dataProvider providerDOM
     */
    function testDOM($php, $xml) {
        $this->assertEquals($php, $xml);
    }

    function providerDOM()
    {
        return new XML_BuilderTestIterator(array('class'=>'dom'));
    }


    /**
     * XMLWriter版で全テストケースを起動
     *
     * @dataProvider providerXMLWriter
     */
//    function testXMLWriter($php, $xml) {
//        $this->assertEquals($php, $xml);
//    }
//    function providerXMLWriter()
//    {
//        return array();
//        //new XML_BuilderTestIterator(array('class'=>'xmlwriter'));
//    }

}


/**
 * テストのデータプロバイダ。
 * 全部配列にするとメモリが不安なのでイテレーターを利用する
 *
 */
class XML_BuilderTestIterator implements Iterator
{
    private $tests, $xmls, $length, $i=0, $builderOption;

    function __construct(array $builderOption) {
        $dir = dirname(__FILE__);

        $this->tests = glob($dir . '/test*.php');
        $this->length = count($this->tests);
        $this->xmls = glob($dir . '/test*.xml');
        $this->builderOption = $builderOption;
    }

    private function _getTest() {
        $builder = xml_builder($this->builderOption);
        ob_start();
            include $this->tests[$this->i];
        return ob_get_clean();
    }

    private function _getXml() {
        return file_get_contents($this->xmls[$this->i]);
    }

    function current() {
        return array(
            $this->_getTest(),
            $this->_getXml()
        );
    }

    function key() {
        return $this->i;
    }

    function next() {
        $this->i++;
    }

    function rewind() {
        $this->i = 0;
    }

    function valid() {
        return $this->i < $this->length;
    }
}
