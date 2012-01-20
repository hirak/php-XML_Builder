<?php
/**
 * XML_Builder
 *
 * XML_Builder_XMLWriter class file
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

if (!class_exists('XML_Builder_Abstract', false)) {
    require_once dirname(__FILE__).'/Abstract.php';
}

/**
 * XML_Builder_XMLWriter
 *
 * XMLWriterのWrapper. DOMよりメモリの消費量が少ない。
 *
 * @category  XML
 * @package   XML_Builder
 * @author    Hiraku NAKANO <hiraku@tojiru.net>
 * @copyright 2012 Hiraku NAKANO
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://openpear.org/packages/XML_Builder
 */
class XML_Builder_XMLWriter extends XML_Builder_Abstract
{
    public $xmlWriter;
    protected $_writeToMemory = false;

    /**
     * @param writeto 書き込み先を指定
     */
    function __construct(array $writer)
    {
        $option = $writer;
        $writer = new XMLWriter;
        if (isset($option['writeto']) && $option['writeto']!=='memory') {
            $writer->openURI($option['writeto']);
        } else {
            $writer->openMemory();
            $this->_writeToMemory = true;
        }
        if ($option['formatOutput']) {
            $writer->setIndentString('  ');
            $writer->setIndent(true);
        }
        $writer->startDocument($option['version'], $option['encoding']);
        if (is_array($option['doctype'])) {
            list($qualifiedName, $publicId, $systemId) = $option['doctype'];
            $writer->writeDTD($qualifiedName, $publicId, $systemId);
        }
        $this->xmlWriter = $writer;
    }

    function xmlElem($name)
    {
        $this->xmlWriter->startElement($name);
        return $this;
    }

    function xmlEnd()
    {
        $this->xmlWriter->endElement();
        return $this;
    }

    function xmlAttr(array $attr=array())
    {
        $writer = $this->xmlWriter;
        foreach ($attr as $label => $value) {
            $writer->writeAttribute($label, $value);
        }
        return $this;
    }

    function xmlCdata($str)
    {
        $this->xmlWriter->writeCData($str);
        return $this;
    }

    function xmlText($str)
    {
        $this->xmlWriter->text($str);
        return $this;
    }

    function xmlComment($str)
    {
        $this->xmlWriter->writeComment($str);
        return $this;
    }

    function xmlPi($target, $data)
    {
        $this->xmlWriter->writePI($target, $data);
        return $this;
    }

    function __toString()
    {
        if ($this->_writeToMemory) {
            return $this->xmlWriter->outputMemory();
        } else {
            return '';
        }
    }

    function xmlEcho()
    {
        echo $this;
    }
}
