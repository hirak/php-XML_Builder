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

    //属性を追加
    public function _ATTR_(array $attr=array())
    {
        $elem =& $this->_elem;
        if ($elem === null) {
            $elem = array();
        } elseif (is_string($elem)) {
            $elem = array('$'=>$elem);
        }
        foreach ($attr as $label => $value) {
            $elem["@$label"] = $value;
        }
        return $this;
    }

    //テキストノードの追加
    public function _TEXT_($str)
    {
        $elem =& $this->_elem;
        if ($elem === null) {
            $elem = $str;
        } elseif (is_string($elem)) {
            $elem .= $str;
        } elseif (isset($elem['$'])) {
            if (is_array($elem['$'])) {
                $elem['$'][] = $str;
            } elseif (is_string($elem['$'])) {
                $elem['$'] .= $str;
            } else {
                $elem['$'] = $str;
            }
        } elseif (is_array($elem)) {
            $elem['$'] = $str;
        }
        return $this;
    }

    //テキストノードの追加と同義
    public function _CDATA_($str)
    {
        $this->_TEXT_($str);
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
        return $this->_render();
    }

    public function _render()
    {
        ob_start();
        var_dump($this->_dom);
        return ob_get_clean();
    }

    public function _echo()
    {
        var_dump($this->_dom);
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
            throw new RuntimeException('missing method');
        }

        $dom =& $this->_dom;
        $elem =& $this->_elem;

        //ラベル名抽出
        if ('_' === $method[strlen($method) - 1]) {
            $label = substr($method, 0, -1);
        } else {
            $label = $method;
        }

        $newelem = null;
        //parent: null
        if ($elem === null) {
            $elem = array($label=>&$newelem);

        //parent: "string"
        } elseif (is_string($elem)) {
            $str = $elem;
            $elem = array('$'=>$str, $label=>&$newelem);

        //parent: [$:[]]
        } elseif (isset($elem['$']) && is_array($elem['$'])) {
            $elem['$'][] = array($label=>&$newelem);

        //parent: [hoge: "fuga"] ($labelがまだ存在しないケース
        } elseif (!array_key_exists($label, $elem)) {
            $elem[$label] =& $newelem;

        //parent: [$label: "hoge"] ($labelが存在し、配列中の末尾であるケース
        } elseif ($this->_lastKey($elem) === $label) {
            if ($this->_isArray($elem[$label])) {
                $elem[$label][] =& $newelem;
            } else {
                $old = $elem[$label];
                $elem[$label] = array($old, &$newelem);
            }

        //parent: [$label: "hoge", hoge: "fuu"] ($labelは存在するが、配列の末尾でないケース
        } else {
            $oldelem = $elem;
            $elem = array('$'=>array());
            foreach ($oldelem as $key => $val) {
                if ($key[0] === '@') {
                    $elem[$key] = $val;
                } else {
                    $elem['$'][] = array($key => $val);
                }
            }
            $elem['$'][] = array($label => &$newelem);
        }

        $childBuilder = new self($dom, $newelem, $this);
        foreach ($args as $arg) {
            if (is_array($arg)) {
                $childBuilder->_ATTR_($arg);
            } else {
                $childBuilder->_TEXT_($arg);
            }
        }

        if ('_' === $method[strlen($method) - 1]) {
            return $this;
        } else {
            return $childBuilder;
        }
    }

    //数値配列かどうか判定する補助メソッド
    // 添字が数値かつ並び順が揃っていたらtrue
    private function _isArray($arr) {
        //違う場合に即座に返せるようあえてfor文で判定
        for (reset($arr), $i=0; list($key)=each($arr);) {
            if ($i++ !== $key) {
                return false;
            }
        }
        return true;
    }

    private function _lastKey(&$arr) {
        end($arr);
        $lastkey = key($arr);
        reset($arr);
        return $lastkey;
    }
}
