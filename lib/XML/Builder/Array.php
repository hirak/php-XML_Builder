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
     * doctypeやオプションは完全に無視する
     *
     */
    public static function __(array $option=array()) {
        $arr = null;
        return new self($arr, $arr);
    }

    public function __construct(&$dom, &$elem=null, &$parent=null)
    {
        $this->_dom =& $dom;
        $this->_elem =& $elem;
        $this->_parent =& $parent;
    }

    public function _()
    {
        return $this->_parent;
    }

    public function __get($name) {
        return $this->$name();
    }

    public function _attr(array $attr=array())
    {
        $elem =& $this->_elem;
        foreach ($attr as $label => $value) {
            $elem["@$label"] = $value;
        }
        return $this;
    }

    //無視
    public function _CDATA_($str)
    {
        return $this;
    }

    public function _TEXT_($str)
    {
        return $this;
    }

    //無視
    public function _COMMENT_($str)
    {
        return $this;
    }

    //無視
    public function _PI_($target, $data)
    {
        return $this;
    }

    public function __toString()
    {
        return print_r($this->_dom, true);
    }

    public function _render()
    {
        return print_r($this->_dom, true);
    }

    public function _echo()
    {
        print_r($this->_dom);
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

        $dom =& $this->_dom;

        ////親要素の初期化
        if ($this->_elem === null) {
            $this->_elem = array();

        } elseif (is_string($this->_elem)) {
            $this->_elem = array('$'=>$this->_elem);
        }

        //単独でappendして終わりの場合
        if ('_' === $method[strlen($method) - 1]) {
            $tag = substr($method, 0, -1);
            $elem = $this->_modify(null, $args);
            $this->_elem[$tag] = $elem;
            return $this;

        //子要素の編集に移る場合
        } else {
            $elem = $this->_modify(null, $args);
            $this->_elem[$method] = $elem;
            return new self($dom, $elem, $this);
        }
    }

    protected function _modify($elem, array $args) {
        if (count($args) === 1 && is_string($args[0])) {
            return $args[0];
        }

        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $label => $value) {
                    $elem["@$label"] = $value;
                }
            } else {
                $elem['$'] = $arg;
            }
        }
        return $elem;
    }

    //数値配列かどうか判定する補助メソッド
    //添字が数値かつ並び順が揃っていたらtrue
    //違う場合に即座に返せるようあえてfor文で判定
    private function isArray($arr) {
        for (reset($arr), $i=0; list($key)=each($arr);) {
            if ($i++ !== $key) {
                return false;
            }
        }
        return true;
    }
}
