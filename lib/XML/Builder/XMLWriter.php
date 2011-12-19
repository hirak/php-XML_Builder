<?php
/**
 * XMLWriter版
 * メモリの消費が少なく、出力先に直接流すことができるが、機能がDOMより制限される
 *
 *
 *
 */

class XML_Builder_XMLWriter extends XML_Builder
{
    public $_writer;
    /**
     * 初期化コード。
     * コンストラクタが多少冗長になるため、コンストラクタとは別物にしてある。
     * @param writeto memory 書き込み先
     *
     */
    public static function _init(array $option=array()) {
        $writer = new XMLWriter;
        if (isset($option['writeto']) && $option['writeto']!=='memory') {
            $writer->openURI($option['writeto']);
        } else {
            $writer->openMemory();
        }
        if ($option['formatOutput']) {
            $writer->setIndentString('  ');
            $writer->setIndent(true);
        }
        $writer->startDocument($option['version'], $option['encoding']);
        if (is_array($option['doctype'])) {
            list($qualifiedName, $publicId, $systemId) = $option['doctype'];
            $writer->writeDTD($qualifiedName, $publicId, $systemId);
        }
        return new self($writer);
    }

    public function __construct($writer)
    {
        $this->_writer = $writer;
    }

    public function _()
    {
        $this->_writer->endElement();
        return $this;
    }

    public function __get($name) {
        return $this->$name();
    }

    public function _attr(array $attr=array())
    {
        $writer = $this->_writer;
        foreach ($attr as $label => $value) {
            $writer->writeAttribute($label, $value);
        }
        return $this;
    }

    public function _cdata($str)
    {
        $this->_writer->writeCData($str);
        return $this;
    }

    public function _text($str)
    {
        $this->_writer->text($str);
        return $this;
    }

    public function _comment($str)
    {
        $this->_writer->writeComment($str);
        return $this;
    }

    public function _pi($target, $data)
    {
        $this->_writer->writePI($target, $data);
        return $this;
    }

    public function __toString()
    {
        return $this->_writer->outputMemory();
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

        $writer = $this->_writer;
        //単独でappendして終わりの場合
        if ('_' === $method[strlen($method) - 1]) {
            $tag = substr($method, 0, -1);
            $writer->startElement($tag);
            $this->_modify($args);
            $writer->endElement();

        //子要素の編集に移る場合
        } else {
            $writer->startElement($method);
            $this->_modify($args);
        }
        return $this;
    }

    protected function _modify(array $args) {
        $writer = $this->_writer;
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $label => $value) {
                    $writer->writeAttribute($label, $value);
                }
            } else {
                $writer->text($arg);
            }
        }
    }
}
