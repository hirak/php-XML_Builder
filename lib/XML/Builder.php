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
        'html',
        '-//W3C//DTD HTML 4.01//EN',
        'http://www.w3.org/TR/html4/strict.dtd',
    );
    static $HTML4_TRANSITIONAL = array(
        'html',
        '-//W3C//DTD HTML 4.01 Transitional//EN',
        'http://www.w3.org/TR/html4/loose.dtd',
    );
    static $HTML4_FRAMESET = array(
        'html',
        '-//W3C//DTD HTML 4.01 Frameset//EN',
        'http://www.w3.org/TR/html4/frameset.dtd',
    );
    static $HTML5 = array('html', null, null);
    static $XHTML1_STRICT = array(
        'html',
        '-//W3C//DTD XHTML 1.0 Strict//EN',
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd',
    );
    static $XHTML1_TRANSITIONAL = array(
        'html',
        '-//W3C//DTD XHTML 1.0 Transitional//EN',
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd',
    );
    static $XHTML1_FRAMESET = array(
        'html',
        '-//W3C//DTD XHTML 1.0 Frameset//EN',
        'http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd'
    );
    static $XHTML11 = array(
        'html',
        '-//W3C//DTD XHTML 1.1//EN',
        'http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd'
    );

    const
        NS_XHTML = 'http://www.w3.org/1999/xhtml'
      , NS_ATOM = 'http://www.w3.org/2005/Atom'
      , NS_ATOM_PUB = 'http://www.w3.org/2007/app'
      , NS_ATOM_THREAD = 'http://purl.org/syndication/thread/1.0'
      , NS_ATOM_HISTORY = 'http://purl.org/syndication/history/1.0'
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

    /**
     * convert "XML string" to Array
     *
     */
    static function xmlToArray($xmlString, $class='XML_Builder_Array') {
        $builder = self::factory(array('class'=>$class));
        $cursor = new XMLReader;
        $cursor->XML($xmlString, null, LIBXML_NOBLANKS);

        while ($cursor->read()) {
            switch ($cursor->nodeType) {
                case XMLReader::ELEMENT:
                    $builder = $builder->xmlElem($cursor->name);
                    if ($cursor->hasAttributes) {
                        $attr = array();
                        $cursor->moveToFirstAttribute();
                        do {
                            $attr[$cursor->name] = $cursor->value;
                        } while($cursor->moveToNextAttribute());
                        $builder->xmlAttr($attr);
                        $cursor->moveToElement();
                    }
                    if ($cursor->isEmptyElement) {
                        $builder = $builder->xmlEnd();
                    }
                    break;
                case XMLReader::END_ELEMENT:
                    $builder = $builder->xmlEnd();
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $builder->xmlText($cursor->value);
                    break;
            }
        }

        return $builder->xmlArray;
    }

    /**
     * convert "DOMDocument" to Array
     *
     */
    static function domToArray(DOMNode $node, $builder='array') {
        static $nsList=array();

        if ($node instanceof DOMDocument) {
            $xpath = new DOMXPath($node);
            $namespaces = $xpath->query('namespace::*');
            $nsList = array();
            foreach ($namespaces as $n) {
                $nsList[] = $n->nodeName;
            }

            if (empty($builder)) {
                $builder = self::factory(array('class'=>'array'));
            } elseif (is_string($builder)) {
                $builder = self::factory(array('class'=>$builder));
            }

            $result = self::domToArray($node->documentElement, $builder);
            return $result->xmlArray;
        }

        $b = $builder->xmlElem($node->nodeName);

        $array = array();
        //名前空間を復元
        foreach ($nsList as $ns) {
            $xmlns = $node->getAttribute($ns);
            if ('' !== $xmlns) {
                $array[$ns] = $xmlns;
            }
        }
        //属性がある場合
        if ($node->hasAttributes()) {
            $attr = $node->attributes;
            for ($i=0, $len=$attr->length; $i<$len; $i++) {
                $currentAttr = $attr->item($i);
                $array[$currentAttr->nodeName] = $currentAttr->nodeValue;
            }
        }
        if (!empty($array)) {
            $b->xmlAttr($array);
        }

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $cn) {
                switch ($cn->nodeType) {
                    case XML_TEXT_NODE:
                        $b->xmlText($cn->nodeValue);
                        break;
                    case XML_CDATA_SECTION_NODE:
                        $b->xmlCdata($cn->nodeValue);
                        break;
                    case XML_COMMENT_NODE:
                        $b->xmlComment($cn->data);
                        break;
                    case XML_ELEMENT_NODE:
                        $b = self::domToArray($cn, $b);
                }
            }
        }

        return $b->xmlEnd();
    }

    //translator for xml
    static function toXsdType($data)
    {
        if (is_bool($data)) {
            return $data ? 'true' : 'false';
        } elseif ($data instanceof DateTime) {
            return $data->format('c');
        } else {
            return $data;
        }
    }

    //translator for json
    static function toJsonType($data)
    {
        if ($data instanceof DateTime) {
            return $data->format('c');
        } elseif (method_exists($data, '__toString')) {
            return (string)$data;
        } else {
            return $data;
        }
    }
}
