<?php

require_once dirname(__FILE__) . '/../lib/XML/Builder.php';

$moge = xml_builder(array('doctype'=>XML_Builder::$HTML4_STRICT));
$moge
    ->feed
        ->moge_('fuga')
        ->moge_
        ->_export($foo);
//チェーンの途中中断

//再開
        $foo
        ->muu
            ->mogemoge('mogemoge',array('abc'=>'bca'))
            ->_
            ->moge
            //即時関数による埋め込み
                ->_do(function($dom){
                    $dom->muu_();
                    $dom->muu_();
                })
            ->_
        ->_
    ->_;

//var_dump($moge);
echo $moge;
