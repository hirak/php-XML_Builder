#!/usr/bin/env phpunit --colors
<?php
/**
 * テストの起動スクリプト
 * ひたすらtest*.phpとtest*.xmlの比較を行います。
 * PHPUnitが必要です。
 *
 */
error_reporting(E_ALL|E_STRICT);

require_once dirname(__FILE__) . '/../lib/XML/Builder.php';
class XML_BuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * 全テストケースを起動
     *
     */
    function testAll() {
        $dir = dirname(__FILE__);
        $tests = glob($dir . '/test*.php');
        $xmls = glob($dir . '/test*.xml');
        $length = min(count($tests), count($xmls));

        for ($i=0; $i<$length; $i++) {
            $builder = XML_Builder::factory();

            $php = include $tests[$i];
            $php = (string)$php;
            $this->assertXmlStringEqualsXmlFile($xmls[$i], $php, $tests[$i]);
        }

        for ($i=0; $i<$length; $i++) {
            $builder = XML_Builder::factory(array('class'=>'xmlwriter'));

            $php = include $tests[$i];
            $php = (string)$php;
            $this->assertXmlStringEqualsXmlFile($xmls[$i], $php, $tests[$i]);
        }

        $arrays = glob($dir . '/test*.php.array');
        $length = min(count($tests), count($arrays));

        for ($i=0; $i<$length; $i++) {
            $builder = XML_Builder::factory(array('class'=>'array'));

            $php = include $tests[$i];
            $arr = include $arrays[$i];
            $this->assertEquals($arr, $php->xmlArray, $tests[$i]);
        }
    }

}
