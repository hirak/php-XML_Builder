<?php
/**
 * XML_Builder interface
 *
 * builder interface
 *
 * PHP versions 5
 *
 * LICENSE: MIT License
 *
 * @category  XML
 * @package   XML_Builder
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @copyright 2012 Hiraku NAKANO
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/packages/XML_Builder
 */

/**
 * XML_Builder_Interface
 *
 * interface
 *
 * @category  XML
 * @package   XML_Builder
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @copyright 2012 Hiraku NAKANO
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/packages/XML_Builder
 */
interface XML_Builder_Interface
{
    function xmlElem($name);

    function xmlEnd();

    function xmlAttr(array $arr);

    function xmlText($str);

    function xmlCdata($str);

    function xmlComment($str);

    function xmlPi($target, $data);

    function xmlDo($callback);

    function xmlExport(&$out);

    function xmlPause(&$out);
}
