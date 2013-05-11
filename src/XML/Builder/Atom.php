<?php
/**
 * XML_Builder_Atom
 *
 * Atom syndication format
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */
class XML_Builder_Atom extends XML_Builder_XMLWriter
{
    protected function xmlFilter($var)
    {
        if (is_bool($var)) {
            return $var ? 'true' : 'false';
        }

        if ($var instanceof DateTime) {
            return $var->format('c');
        }

        return $var;
    }
}
