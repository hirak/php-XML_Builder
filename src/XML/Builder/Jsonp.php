<?php
/**
 * XML_Builder_Jsonp
 *
 * XML_Builderの具象クラス・JSONP版
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */

if (!class_exists('XML_Builder_Json', false)) {
    require_once dirname(__FILE__).'/Json.php';
}

class XML_Builder_Jsonp extends XML_Builder_Json
{
    protected $_callback = 'callback';

    function __construct(&$array, &$elem=null, &$parent=null)
    {
        if ($parent === null) {
            //初期
            if (isset($array['callback'])) {
                if (! preg_match('/^[\w$][\w\d.[\]$]*$/', $array['callback'])) {
                    throw new InvalidArgumentException('callback is invalid: ' . $array['callback']);
                }
                $this->_callback = $array['callback'];
            }
        }
        parent::__construct($array, $elem, $parent);
    }

    function xmlElem($name, $class=__CLASS__)
    {
        return parent::xmlElem($name, $class);
    }

    function xmlRender()
    {
        return $this->_callback . '(' . parent::xmlRender() . ')';
    }
}
