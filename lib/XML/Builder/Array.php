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

    //高速化のため、タイプ判定をキャッシュして使いまわす。
    public $_type = self::TYPE_NULL, $_lastKey = null;

    const TYPE_NULL       = 0 //null
        , TYPE_STRING     = 1 //"str"
        , TYPE_D_STRING   = 2 //"$":"str"
        , TYPE_D_ARRAY    = 3 //"$":[]
        , TYPE_ATTR_ARRAY = 4 //"@attr":"attr",...
        , TYPE_ARRAY      = 5 //"hoge":"fuga",...
        ;

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
        switch ($this->_type) {
        case self::TYPE_NULL:
            $elem = array();
            $this->_type = self::TYPE_ATTR_ARRAY;
            break;
        case self::TYPE_STRING:
            $string = $elem;
            $elem = array('$'=>$string);
            $this->_type = self::TYPE_D_STRING;
            break;
        }
        foreach ($attr as $label => $value) {
            $elem["@$label"] = $value;
        }
        $this->_lastKey = "@$label";
        return $this;
    }

    //テキストノードの追加
    public function xmlText($str)
    {
        $elem =& $this->xmlCurrentElem;
        switch ($this->_type) {
        case self::TYPE_NULL:
            $elem = $str;
            $this->_type = self::TYPE_STRING;
            break;
        case self::TYPE_STRING:
            $elem .= $str;
            break;
        case self::TYPE_D_ARRAY:
            $elem['$'][] = $str;
            break;
        case self::TYPE_D_STRING:
            $elem['$'] .= $str;
            break;
        case self::TYPE_ATTR_ARRAY:
            $elem['$'] = $str;
            $this->_type = self::TYPE_D_STRING;
            break;
        case self::TYPE_ARRAY:
            $elem['$'] = array();
            foreach ($elem as $key => $val) {
                if ($key[0] !== '@') {
                    $elem['$'][] = array($key=>$val);
                }
            }
            $elem['$'][] = $str;
            $this->_type = self::TYPE_D_ARRAY;
            break;
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

        switch ($this->_type) {
        case self::TYPE_NULL:
            $elem = array($name => &$newelem);
            $this->_type = self::TYPE_ARRAY;
            $this->_lastKey = $name;
            break;
        case self::TYPE_STRING:
            $str = $elem;
            $elem = array('$' => array($str, array($name => &$newelem)));
            $this->_type = self::TYPE_D_ARRAY;
            $this->_lastKey = '$';
            break;
        case self::TYPE_D_STRING:
            $str = $elem['$'];
            $elem['$'] = array($str, array($name => &$newelem));
            $this->_type = self::TYPE_D_ARRAY;
            $this->_lastKey = '$';
            break;
        case self::TYPE_ATTR_ARRAY:
            $elem[$name] =& $newelem;
            $this->_type = self::TYPE_ARRAY;
            $this->_lastKey = $name;
            break;
        case self::TYPE_ARRAY:
            if ($name === $this->_lastKey) {
                if (is_array($elem[$name]) && key($elem[$name]) === 0) { //数値配列とみなす
                    $elem[$name][] =& $newelem;
                } else {
                    $original = $elem[$name];
                    $elem[$name] = array($original, &$newelem);
                }
            } elseif (array_key_exists($name, $elem)) {
                //TYPE_D_ARRAYへ変更する
                $elem['$'] = array();
                foreach ($elem as $key => $val) {
                    if ($key[0] !== '@') {
                        if (is_array($val) && key($val) === 0) { //hoge:[1,2,3]は展開する
                            foreach ($val as $v) {
                                $elem['$'][] = array($key => $v);
                            }
                        } else {
                            $elem['$'][] = $val;
                        }
                        unset($elem[$key]);
                    }
                }
                $this->_type = self::TYPE_D_ARRAY;
                $this->_lastKey = '$';
            } else {
                $elem[$name] =& $newelem;
                $this->_lastKey = $name;
            }
            break;
        case self::TYPE_D_ARRAY:
            $elem['$'][] = array($name => &$newelem);
            break;
        }

        return new self($dom, $newelem, $this);
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
