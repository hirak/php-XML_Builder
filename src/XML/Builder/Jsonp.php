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

    function __construct(array $option)
    {
        if (isset($option['callback'])) {
            if (! preg_match('/^[\w$][\w\d.[\]$]*$/', $option['callback'])) {
                throw new InvalidArgumentException('callback is invalid: ' . $option['callback']);
            }
            $this->_callback = $option['callback'];
        }
        parent::__construct($option);
    }

    function xmlRender()
    {
        return $this->_callback . '(' . parent::xmlRender() . ')';
    }
}
