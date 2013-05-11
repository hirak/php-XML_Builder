<?php
/**
 * XML_Builder_Json
 *
 * XML_Builderの具象クラス・JSON版
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */
if (!class_exists('XML_Builder_Array', false)) {
    require_once dirname(__FILE__).'/Array.php';
}

class XML_Builder_Json extends XML_Builder_Array
{
    protected $_serializer = 'XML_Builder::json';

    protected function xmlFilter($var)
    {
        if ($var instanceof DateTime) {
            $var = $var->format('c');
        }

        return $var;
    }
}
