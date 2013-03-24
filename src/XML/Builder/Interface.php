<?php
/**
 * XML_Builder interface
 *
 * builder interface
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE.md MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */

/**
 * XML_Builder_Interface
 *
 * interface
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE.md MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */
interface XML_Builder_Interface
{
    function xmlElem($name);

    function xmlEnd();

    function xmlAttr(array $attr=array());

    function xmlText($str);

    function xmlCdata($str);

    function xmlComment($str);

    function xmlPi($target, $data);

    function xmlDo($callback);

    function xmlExport(&$out);

    function xmlPause(&$out);

    function xmlMarkArray($name);

    function xmlRaw($xml);
}
