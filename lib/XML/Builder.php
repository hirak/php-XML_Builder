<?php
/**
 * XML_Builder
 *
 * utility & constants class
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

if (!class_exists('XML_Builder_Interface', false)) {
    require_once dirname(__FILE__).'/Builder/Interface.php';
}
if (!class_exists('XML_Builder_Abstract', false)) {
    require_once dirname(__FILE__).'/Builder/Abstract.php';
}
if (!class_exists('XML_Builder_DOM', false)) {
    require_once dirname(__FILE__).'/Builder/DOM.php';
}
if (!class_exists('XML_Builder_XMLWriter', false)) {
    require_once dirname(__FILE__).'/Builder/XMLWriter.php';
}
if (!class_exists('XML_Builder_Array', false)) {
    require_once dirname(__FILE__).'/Builder/Array.php';
}

/**
 * XML_Builder
 *
 * utility & constants
 *
 * @category  XML
 * @package   XML_Builder
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @copyright 2012 Hiraku NAKANO
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/packages/XML_Builder
 */
abstract class XML_Builder
{
    static $sigil = '_';

    static $HTML4_STRICT = array(
        'HTML',
        '-//W3C//DTD HTML 4.01//EN',
        'http://www.w3.org/TR/html4/strict.dtd',
    );
    static $HTML4_TRANSITIONAL = array(
        'HTML',
        '-//W3C//DTD HTML 4.01 Transitional//EN',
        'http://www.w3.org/TR/html4/loose.dtd',
    );
    static $HTML4_FRAMESET = array(
        'HTML',
        '-//W3C//DTD HTML 4.01 Frameset//EN',
        'http://www.w3.org/TR/html4/frameset.dtd',
    );
    static $HTML5 = array('HTML', null, null);
    static $XHTML1_STRICT = array(
        'XHTML',
        '-//W3C//DTD XHTML 1.0 Strict//EN',
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd',
    );
    static $XHTML1_TRANSITIONAL = array(
        'XHTML',
        '-//W3C//DTD XHTML 1.0 Transitional//EN',
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd',
    );
    static $XHTML1_FRAMESET = array(
        'XHTML',
        '-//W3C//DTD XHTML 1.0 Frameset//EN',
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd'
    );
    static $XHTML11 = array(
        'XHTML',
        '-//W3C//DTD XHTML 1.1//EN',
        'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'
    );

    const
        NS_XHTML = 'http://www.w3.org/1999/xhtml'
      , NS_ATOM = 'http://www.w3.org/2005/Atom'
      , NS_ATOM_PUB = 'http://www.w3.org/2007/app'
      , NS_OPENSEARCH = 'http://a9.com/-/spec/opensearch/1.1/'
      , NS_GDATA = 'http://schemas.google.com/g/2005'
      , NS_RSS_10 = 'http://purl.org/rss/1.0/'
      , NS_DC = 'http://purl.org/dc/elements/1.1/'
      , NS_XSLT = 'http://www.w3.org/1999/XSL/Transform'
    ;

    /**
     * create instance
     *
     * @param array $option parameters for XML_Builder_Interface
     *
     * @return XML_Builder_Interface
     */
    static function factory(array $option=array())
    {
        $option += array(
            'version' => '1.0',
            'encoding' => 'UTF-8',
            'formatOutput' => true,
            'doctype' => null,
            'class' => 'XML_Builder_DOM'
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
        return new $class($option);
    }
}
