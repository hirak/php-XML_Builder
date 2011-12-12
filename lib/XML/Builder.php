<?php
/**
 * factory function
 *
 *
 */

if (!class_exists('XML_Builder_DOM', false)) {
    require_once dirname(__FILE__).'/Builder/DOM.php';
}
if (!class_exists('XML_Builder_XMLWriter', false)) {
    require_once dirname(__FILE__).'/Builder/XMLWriter.php';
}
if (!class_exists('XML_Builder_Array', false)) {
    require_once dirname(__FILE__).'/Builder/Array.php';
}

function xml_builder(array $option=array()) {
    $option += array(
        'version' => '1.0',
        'encoding' => 'UTF-8',
        'formatOutput' => true,
        'doctype' => null,
        'class' => 'XML_Builder_DOM',
    );

    $classmap = array(
        'dom' => 'XML_Builder_DOM',
        'xmlwriter' => 'XML_Builder_XMLWriter',
        'array' => 'XML_Builder_Array',
    );
    if (isset($classmap[$option['class']])) {
        $option['class'] = $classmap[$option['class']];
    }

    $class = $option['class'];
    return $class::__($option);
}

/**
 * XML_Builderの基底クラス
 *
 *
 */
abstract class XML_Builder
{
    public static
        $HTML4_STRICT = array('HTML', '-//W3C//DTD HTML 4.01//EN', 'http://www.w3.org/TR/html4/strict.dtd')
      , $HTML4_TRANSITIONAL = array('HTML', '-//W3C//DTD HTML 4.01 Transitional//EN', 'http://www.w3.org/TR/html4/loose.dtd')
      , $HTML4_FRAMESET = array('HTML', '-//W3C//DTD HTML 4.01 Frameset//EN', 'http://www.w3.org/TR/html4/frameset.dtd')
      , $HTML5 = array('html', '', '')
      , $XHTML1_STRICT = array('XHTML', '-//W3C//DTD XHTML 1.0 Strict//EN', 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd')
      , $XHTML1_TRANSITIONAL = array('XHTML', '-//W3C//DTD XHTML 1.0 Transitional//EN', 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd')
      , $XHTML1_FRAMESET = array('XHTML', '-//W3C//DTD XHTML 1.0 Frameset//EN', 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd')
      , $XHTML11 = array('XHTML', '-//W3C//DTD XHTML 1.1//EN', 'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd')
    ;
    const
        NS_XHTML = 'http://www.w3.org/1999/xhtml'
      , NS_ATOM = 'http://www.w3.org/2005/Atom'
      , NS_ATOM_PUB = 'http://www.w3.org/2007/app'
      , NS_OPENSEARCH = 'http://a9.com/-/spec/opensearch/1.1/'
      , NS_GDATA = 'http://schemas.google.com/g/2005'
      , NS_RSS_10 = 'http://purl.org/rss/1.0/'
      , NS_Dublin_Core = ''
    ;
    public $_dom;
    public $_elem;
    public $_parent;

    /**
     * 終端子の処理
     */
    abstract public function _();

    public function __get($name) {
        return $this->$name();
    }

    /**
     * 属性を追加
     */
    abstract public function _ATTR_(array $attr=array());

    /**
     * CDATAセクションを追加
     */
    abstract public function _CDATA_($str);

    /**
     * テキストノードを追加
     */
    abstract public function _TEXT_($str);

    /**
     * コメントノードを追加
     */
    abstract public function _COMMENT_($str);

    /**
     * ProcessingInstructionを追加
     */
    abstract public function _PI_($target, $data);

    abstract public function __toString();

    public function _echo() {
        echo $this;
        return $this;
    }

    /**
     * 引数にthisを渡して処理を中断できるようにする
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

}
