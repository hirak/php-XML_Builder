<?php
/**
 * XML_Builderと見せかけてPHPの配列を作るだけのクラス。
 * 同じインターフェースで出力を差し替えたい場合にどうぞ。
 *
 *
 */
if (!class_exists('XML_Builder_Abstract',false)) require_once dirname(__FILE__).'/Abstract.php';
class XML_Builder_Array extends XML_Builder_Abstract
{
    public $xmlArray, $xmlCurrentElem, $xmlParent;

    public function __construct()
    {
        if (func_num_args() === 1) {
            $this->xmlArray = null;
            $this->xmlCurrentElem = null;
            return;
        }
        $this->xmlArray =& func_get_arg(0);
        $this->xmlCurrentElem =& func_get_arg(1);
        $this->xmlParent =& func_get_arg(2);
    }

    public function xmlEnd()
    {
        return $this->xmlParent;
    }

    //属性を追加
    public function xmlAttr(array $attr=array())
    {
        $elem =& $this->xmlCurrentElem;
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
    public function xmlText($str)
    {
        $elem =& $this->xmlCurrentElem;
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
    public function xmlCdata($str)
    {
        $this->xmlText($str);
        return $this;
    }

    //無視
    public function xmlComment($str)
    {
        return $this;
    }

    //無視
    public function xmlPi($target, $data)
    {
        return $this;
    }

    public function __toString()
    {
        return $this->xmlRender();
    }

    public function xmlRender()
    {
        ob_start();
        var_dump($this->xmlArray);
        return ob_get_clean();
    }

    public function xmlEcho()
    {
        var_dump($this->xmlArray);
        return $this;
    }

    public function xmlElem($method)
    {
        if ('_' === $method[0]) {
            throw new RuntimeException('missing method');
        }

        $dom =& $this->xmlArray;
        $elem =& $this->xmlCurrentElem;

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
                $childBuilder->xmlAttr($arg);
            } else {
                $childBuilder->xmlText($arg);
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

    //配列の末尾のキーを取得
    private function _lastKey(&$arr) {
        end($arr);
        $lastkey = key($arr);
        reset($arr);
        return $lastkey;
    }
}
