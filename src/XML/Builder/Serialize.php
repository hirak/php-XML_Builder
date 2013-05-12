<?php
/**
 * XML_Builder_Serialize
 *
 * PHPSerialize
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */

class XML_Builder_Serialize extends XML_Builder_Array
{
    protected $_serializer = 'serialize';

    protected function xmlFilter($var)
    {
        /*
         * MEMO: DateTime is unserializable until PHP 5.3.
         */
        if ($var instanceof DateTime) {
            $var = $var->format('c');
        }
        return $var;
    }
}
