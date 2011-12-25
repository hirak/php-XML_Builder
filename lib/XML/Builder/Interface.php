<?php

interface XML_Builder_Interface
{
    function xmlElem($name);

    function xmlEnd();

    function xmlAttr(array $arr);

    function xmlText($str);

    function xmlCdata($str);

    function xmlComment($str);

    function xmlPi($target, $data);

    /**
     * 任意のコールバックを実行する
     *
     */
    function xmlDo($callback);

    /**
     * 現在の$thisを出力してメソッドチェーンを中断する
     *
     */
    function xmlExport(&$out);

    /**
     *
     */
    function xmlPause(&$out);
}
