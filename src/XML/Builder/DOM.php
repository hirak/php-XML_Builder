<?php
/**
 * XML_Builder
 *
 * XML_Builder_DOM class
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE.md MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */

if (!class_exists('XML_Builder_Abstract', false)) {
    require_once dirname(__FILE__).'/Abstract.php';
}

/**
 * XML_Builder_DOM
 *
 * DOMDocumentのWrapper
 *
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @license   https://github.com/hirak/php-XML_Builder/blob/master/LICENSE.md MIT License
 * @link      https://packagist.org/packages/hiraku/xml_builder
 */
class XML_Builder_DOM extends XML_Builder_Abstract
{
    public $xmlDom, $xmlCurrentElem, $xmlParent;

    function __construct()
    {
        if (func_num_args() === 1) {
            $option = func_get_arg(0);
            if (is_array($option['doctype'])) {
                list($qualifiedName, $publicId, $systemId) = $option['doctype'];
                $impl = new DOMImplementation;
                $dtype = $impl->createDocumentType(
                    $qualifiedName,
                    $publicId,
                    $systemId
                );
                $dom = $impl->createDocument(null, null, $dtype);
                $dom->formatOutput = $option['formatOutput'];
                $dom->resolveExternals = true;
                $dom->xmlVersion = $option['version'];
                $dom->encoding = $option['encoding'];
            } else {
                $dom = new DOMDocument($option['version'], $option['encoding']);
                $dom->formatOutput = $option['formatOutput'];
            }
            $this->xmlDom = $dom;
            $this->xmlCurrentElem = $dom;
        } else {
            list(
                $this->xmlDom,
                $this->xmlCurrentElem,
                $this->xmlParent
            ) = func_get_args();
        }
    }

    function xmlElem($name)
    {
        $elem = $this->xmlDom->createElement($name);
        $this->xmlCurrentElem->appendChild($elem);
        return new self($this->xmlDom, $elem, $this);
    }

    function xmlEnd()
    {
        return $this->xmlParent;
    }

    function xmlAttr(array $attr=array())
    {
        $elem = $this->xmlCurrentElem;
        foreach ($attr as $label => $value) {
            $elem->setAttribute($label, $value);
        }
        return $this;
    }

    function xmlCdata($str)
    {
        $cdata = $this->xmlDom->createCDATASection($str);
        $this->xmlCurrentElem->appendChild($cdata);
        return $this;
    }

    function xmlText($str)
    {
        $text = $this->xmlDom->createTextNode($str);
        $this->xmlCurrentElem->appendChild($text);
        return $this;
    }

    function xmlComment($str)
    {
        $comment = $this->xmlDom->createComment($str);
        $this->xmlCurrentElem->appendChild($comment);
        return $this;
    }

    function xmlPi($target, $data)
    {
        $pi = $this->xmlDom->createProcessingInstruction($target, $data);
        $this->xmlCurrentElem->appendChild($pi);
        return $this;
    }

    function xmlRaw($xml)
    {
        $df = $this->xmlDom->createDocumentFragment();
        $df->appendXML($xml);
        $this->xmlCurrentElem->appendChild($df);
        return $this;
    }

    function xmlRender($format='xml')
    {
        if ($format === 'html') {
            return $this->xmlDom->saveHTML();
        } else {
            return $this->xmlDom->saveXML();
        }
    }

    function xmlEcho($format='xml')
    {
        echo $this->xmlRender($format);
        return $this;
    }

    function __toString()
    {
        return $this->xmlRender();
    }
}
