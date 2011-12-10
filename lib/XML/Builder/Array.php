<?php
/**
 * XML_Builderと見せかけてPHPの配列を作るだけのクラス。
 * 同じインターフェースで出力を差し替えたい場合にどうぞ。
 *
 *
 */

class XML_Builder_Array extends XML_Builder
{
    /**
     * 初期化コード。
     * コンストラクタが多少冗長になるため、コンストラクタとは別物にしてある。
     *
     */
    public static function __(array $option=array()) {
        if (is_array($option['doctype'])) {
            list($qualifiedName, $publicId, $systemId) = $option['doctype'];
            $impl = new DOMImplementation;
            $dtype = $impl->createDocumentType($qualifiedName, $publicId, $systemId);
            $dom = $impl->createDocument(null, null, $dtype);
            $dom->formatOutput = $option['formatOutput'];
            $dom->resolveExternals = true;
            $dom->xmlVersion = $option['version'];
            $dom->encoding = $option['encoding'];
        } else {
            $dom = new DOMDocument($option['version'], $option['encoding']);
            $dom->formatOutput = $option['formatOutput'];
        }
        return new self($dom, $dom);
    }

    public function __construct($dom, $elem=null, $parent=null)
    {
        $this->_dom = $dom;
        $this->_elem = $elem;
        $this->_parent = $parent;
    }

    public function _()
    {
        return $this->_parent;
    }

    public function _insertBefore()
    {
        $this->_parent->_elem->insertBefore(
            $this->_elem
        );
        return $this->_parent;
    }

    public function __get($name) {
        return $this->$name();
    }

    public function _attr(array $attr=array())
    {
        $elem = $this->_elem;
        foreach ($attr as $label => $value) {
            $elem->setAttribute($label, $value);
        }
        return $this;
    }

    public function _CDATA_($str)
    {
        $cdata = $this->_dom->createCDATASection($str);
        $this->_elem->appendChild($cdata);
        return $this;
    }

    public function _TEXT_($str)
    {
        $text = $this->_dom->createTextNode($str);
        $this->_elem->appendChild($text);
        return $this;
    }

    public function _COMMENT_($str)
    {
        $comment = $this->_dom->createComment($str);
        $this->_elem->appendChild($comment);
        return $this;
    }

    public function _PI_($target, $data)
    {
        $pi = $this->_dom->createProcessingInstruction($target, $data);
        $this->_elem->appendChild($pi);
        return $this;
    }

    public function _toHTML()
    {
        return $this->_dom->saveHTML();
    }

    public function _toXML()
    {
        return $this->_dom->saveXML();
    }

    public function __toString()
    {
        return $this->_dom->saveXML();
    }

    public function _echoXML()
    {
        echo $this->_dom->saveXML();
        return $this;
    }

    public function _echoHTML()
    {
        echo $this->_dom->saveHTML();
        return $this;
    }

    /**
     * 引数にthisを渡して処理を中断できるようにする
     *
     *
     */
    public function _export(&$ref)
    {
        $ref = $this;
        return $this;
    }

    /**
     * thisを引数に渡して任意のコールバックを実行する
     *
     */
    public function _do($callback)
    {
        if (is_callable($callback)) {
            call_user_func($callback, $this);
            return $this;
        }
        throw new RuntimeException();
    }

    public function __call($method, $args)
    {
        if ('_' === $method[0]) {
            throw new RuntimeException('そんなメソッドないよ');
        }

        $dom = $this->_dom;
        //単独でappendして終わりの場合
        if ('_' === $method[strlen($method) - 1]) {
            $tag = substr($method, 0, -1);
            $elem = $this->_modify($dom->createElement($tag), $args);
            $this->_elem->appendChild($elem);
            return $this;

        //子要素の編集に移る場合
        } else {
            $elem = $this->_modify($dom->createElement($method), $args);
            return new self($dom, $elem, $this);
        }
    }

    protected function _modify(DOMNode $elem, array $args) {
        $dom = $this->_dom;
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $label => $value) {
                    $elem->setAttribute($label, $value);
                }
            } else {
                $elem->appendChild($dom->createTextNode($arg));
            }
        }
        return $elem;
    }
}
