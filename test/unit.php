<?php

require_once dirname(__FILE__) . '/../lib/XML/Builder.php';

$builder = xml_builder(array('doctype'=>XML_Builder::$HTML5));

$builder

->html
  ->head
    ->meta_(array('http-equiv'=>'Content-Type', 'content'=>'text/html; charset=UTF-8'))
    ->_export($foo);
//チェーンの途中中断

//再開
    $foo
    ->title_('日本語')
    ->link_(array('rel'=>'stylesheet'))
  ->_

  ->body
    ->div
      ->h1_('h1')

      //即時関数による埋め込み
      ->_do(function($chain){
          $chain->div_('ああああああ');
      })

    ->_
  ->_
->_

->_export($html)
;

//var_dump($moge);
echo $html->_render('html');
