XML_Builder
======================================================

[![Build Status](https://travis-ci.org/hirak/php-XML_Builder.png?branch=master)](https://travis-ci.org/hirak/php-XML_Builder)

XMLの生成コードを短く書くためのライブラリです。
http://hirak.github.com/xmlbuilder/

[例]
------------------------------------------------------

以下のコードを実行すると、XMLが吐き出されます。

```php
<?php
require_once 'XML/Builder.php';

xml_builder(array('doctype'=>XML_Builder::$XHTML11))
->html(array('xmlns'=>XML_Builder::NS_XHTML))
    ->head
        ->meta_(array('http-equiv'=>'Content-Type','content'=>'text/html; charset=UTF-8'))
        ->title_('サンプルHTML')
    ->_
    ->body
        ->div(array('id'=>'wrapper'))
            ->h1_('サンプルHTML')
            ->p_('サンプル')
        ->_
    ->_
->_

->_echo();
```

[特徴]
------------------------------------------------------

* 選べるバックエンド ・・・DOMかXMLWriterを選べます。
* 独自のDSL ・・・XMLを文字列で書くより短く書けます。
* 安心 ・・・DOMやXMLWriterのWrapperに徹しており、これらをきちんと使うことで安全なXMLを生成できます。
* 配列とDOMの相互変換機能

詳細はドキュメントを参照
http://hirak.github.com/xmlbuilder/


LISENCE
------------------------------------------------------

MIT Lisense.
