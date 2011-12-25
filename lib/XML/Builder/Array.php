<?php
/**
 * XML_Builderと見せかけてPHPの配列を作るだけのクラス。
 * 同じインターフェースで出力を差し替えたい場合にどうぞ。
 *
 *
 */
if (!class_exists('XML_Builder_Abstract',false)) require_once dirname(__FILE__).'/Abstract.php';
//for version < PHP5.4
if (!interface_exists('JsonSerializable',false)) {
    interface JsonSerializable {
        function jsonSerialize();
    }
}
class XML_Builder_Array extends XML_Builder_Abstract implements JsonSerializable
{
    public $xmlArray, $xmlCurrentElem, $xmlParent;

    public function __construct(&$array, &$elem=null, &$parent=null)
    {
        if ($parent === null) {
            $newelem = null;
            $this->xmlArray =& $newelem;
            $this->xmlCurrentElem =& $newelem;
            return;
        }
        $this->xmlArray =& $array;
        $this->xmlCurrentElem =& $elem;
        $this->xmlParent =& $parent;
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

    public function xmlElem($name)
    {
        $dom =& $this->xmlArray;
        $elem =& $this->xmlCurrentElem;

        $newelem = null;
        //parent: null
        if ($elem === null) {
            $elem = array($name=>&$newelem);

        //parent: "string"
        } elseif (is_string($elem)) {
            $str = $elem;
            $elem = array('$'=>array($str, array($name=>&$newelem)));

        //parent: [$:[]]
        } elseif (isset($elem['$']) && is_array($elem['$'])) {
            $elem['$'][] = array($name=>&$newelem);

        //parent: [hoge: "fuga"] ($nameがまだ存在しないケース
        } elseif (!array_key_exists($name, $elem)) {
            $elem[$name] =& $newelem;

        //parent: [$name: "hoge"] ($nameが存在し、配列中の末尾であるケース
        } elseif ($this->_lastKey($elem) === $name) {
            if ($this->_isArray($elem[$name])) {
                $elem[$name][] =& $newelem;
            } else {
                $old = $elem[$name];
                $elem[$name] = array($old, &$newelem);
            }

        //parent: [$name: "hoge", hoge: "fuu"] ($nameは存在するが、配列の末尾でないケース
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
            $elem['$'][] = array($name => &$newelem);
        }

        return new self($dom, $newelem, $this);
    }

    //数値配列かどうか判定する補助メソッド
    // 添字が数値かつ並び順が揃っていたらtrue
    private function _isArray($arr) {
        if (!is_array($arr)) return false;
        //違う場合に即座に返せるようあえてfor文で判定
        for (reset($arr), $i=0; list($key)=each($arr);) {
            if ($i++ !== $key) return false;
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

    //for PHP5.4(json_encode)
    public function jsonSerialize() {
        return $this->xmlArray;
    }

    //for Zend_Json
    public function toJson() {
        return json_encode($this->xmlArray);
    }
}
