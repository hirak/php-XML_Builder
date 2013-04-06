<?php
/**
 * XML_Builder_Serialize
 *
 * XML_Builderの具象クラス・PHPSerialize版
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */

class XML_Builder_Serialize extends XML_Builder_Array
{
    protected $_serializer = 'XML_Builder::serialize';
}
