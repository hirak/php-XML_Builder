<?php
/**
 * XMLWriter版
 * メモリの消費が少なく、出力先に直接流すことができるが、機能がDOMより制限される
 *
 *
 */

if (!class_exists('XML_Builder_Abstract',false)) require_once dirname(__FILE__).'/Abstract.php';
class XML_Builder_XMLWriter extends XML_Builder_Abstract
{
    public $xmlWriter;

    /**
     * @param writeto 書き込み先を指定
     */
    public function __construct(array $writer)
    {
        $option = $writer;
        $writer = new XMLWriter;
        if (isset($option['writeto']) && $option['writeto']!=='memory') {
            $writer->openURI($option['writeto']);
        } else {
            $writer->openMemory();
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

    public function xmlElem($name)
    {
        $this->xmlWriter->startElement($name);
        return $this;
    }

    public function xmlEnd()
    {
        $this->xmlWriter->endElement();
        return $this;
    }

    public function xmlAttr(array $attr=array())
    {
        $writer = $this->xmlWriter;
        foreach ($attr as $label => $value) {
            $writer->writeAttribute($label, $value);
        }
        return $this;
    }

    public function xmlCdata($str)
    {
        $this->xmlWriter->writeCData($str);
        return $this;
    }

    public function xmlText($str)
    {
        $this->xmlWriter->text($str);
        return $this;
    }

    public function xmlComment($str)
    {
        $this->xmlWriter->writeComment($str);
        return $this;
    }

    public function xmlPi($target, $data)
    {
        $this->xmlWriter->writePI($target, $data);
        return $this;
    }

    public function __toString()
    {
        return $this->xmlWriter->outputMemory();
    }
}
