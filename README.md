h1. XML_Builder

XMLの生成をかつてないほど短く書けるライブラリです。

h2. 例

```php
<?php
require_once 'path/to/XML/Builder.php';

xml_builder(array('doctype'=>XML_Builder::XHTML11))
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

h2. 特徴

* 選べるバックエンド ・・・DOMかXMLWriterを選べます。
* 独自のDSL ・・・XMLを文字列で書くより短く書けます。
* 安心 ・・・DOMやXMLWriterのWrapperに徹しており、これらをきちんと使うことで安全なXMLを生成できます。


h2. LISENCE

MIT Lisense.
