<?php

$start = microtime(true);
require_once dirname(__FILE__) . '/../lib/XML/Builder.php';

$builder = xml_builder(array(
    'class'=>'dom',
    'doctype'=>XML_Builder::$XHTML11
));

$builder
->html(array('xmlns' => XML_Builder::NS_XHTML, 'xml:lang' => 'ja'))
    ->head
        ->title_('日本語')
        ->meta_(array('http-equiv'=>'Content-Type', 'content'=>'text/html; charset=UTF-8'))
        ->meta_(array('name'=>'keyword', 'content'=>'デベロッパー, べろべろばー'))
        ->link_(array('rel'=>'stylesheet'))
        ->_export($moge);

        $moge
        ->_do(function($builder){
            $builder->abababa_('abc');
            $builder->mogemoge_('あははは');
        })
    ->_
    ->body
        ->div
            ->h1_('ほげほげ', array('class'=>'muuu'))
        ->_
    ->_
->_
;

//echo serialize($builder->_dom['html']);
echo $builder->_render();

echo "\n", microtime(true) - $start;
