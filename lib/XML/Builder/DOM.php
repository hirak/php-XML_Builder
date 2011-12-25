<?php
/**
 * XML_Builder_DOM
 *
 *
 */

if (!class_exists('XML_Builder_Abstract',false)) require_once dirname(__FILE__).'/Abstract.php';
class XML_Builder_DOM extends XML_Builder_Abstract
{
    public $xmlDom, $xmlCurrentElem, $xmlParent;

    public function __construct()
    {
        if (func_num_args() === 1) {
            $option = func_get_arg(0);
            if (is_array($option['doctype'])) {
                list($qualifiedName, $publicId, $systemId) = $option['doctype'];
                $impl = new DOMImplementation;
                $dtype = $impl->createDocumentType($qualifiedName, $publicId, $systemId);
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

    public function xmlElem($name)
    {
        $elem = $this->xmlDom->createElement($name);
        $this->xmlCurrentElem->appendChild($elem);
        return new self($this->xmlDom, $elem, $this);
    }

    public function xmlEnd()
    {
        return $this->xmlParent;
    }

    public function xmlAttr(array $attr=array())
    {
        $elem = $this->xmlCurrentElem;
        foreach ($attr as $label => $value) {
            $elem->setAttribute($label, $value);
        }
        return $this;
    }

    public function xmlCdata($str)
    {
        $cdata = $this->xmlDom->createCDATASection($str);
        $this->xmlCurrentElem->appendChild($cdata);
        return $this;
    }

    public function xmlText($str)
    {
        $text = $this->xmlDom->createTextNode($str);
        $this->xmlCurrentElem->appendChild($text);
        return $this;
    }

    public function xmlComment($str)
    {
        $comment = $this->xmlDom->createComment($str);
        $this->xmlCurrentElem->appendChild($comment);
        return $this;
    }

    public function xmlPi($target, $data)
    {
        $pi = $this->xmlDom->createProcessingInstruction($target, $data);
        $this->xmlCurrentElem->appendChild($pi);
        return $this;
    }

    public function xmlRender($format='xml')
    {
        if ($format === 'html') {
            return $this->xmlDom->saveHTML();
        } else {
            return $this->xmlDom->saveXML();
        }
    }

    public function xmlEcho($format='xml')
    {
        echo $this->xmlRender($format);
        return $this;
    }

    public function __toString()
    {
        return $this->xmlRender();
    }
}
