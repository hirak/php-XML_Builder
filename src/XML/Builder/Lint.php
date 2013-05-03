<?php
/**
 * Lint Class
 *
 */
if (! class_exists('XML_Builder_Abstract', false)) {
    require_once dirname(__FILE__) . 'Abstract.php';
}

class XML_Builder_Lint extends XML_Builder_Abstract
{
    protected $_stack = array(), $_error=false, $_rootNode=false;

    function xmlElem($name)
    {
        if (! $this->xmlIsStringCastable($name)) {
            $this->_error = true;
            throw new DomainException('$name must be string-castable.');
        }

        if (empty($this->_stack) && $this->_rootNode) {
            throw new DomainException('Root node must be one.');
        }
        array_push($this->_stack, $name);

        $this->_rootNode = true;
        return $this;
    }

    function xmlEnd()
    {
        array_pop($this->_stack);
        return $this;
    }

    function xmlAttr(array $attr=array())
    {
        foreach ($attr as $name => $value) {
            if (! $this->xmlIsStringCastable($value)) {
                $this->_error = true;
                throw new DomainException("\$name($name) must be string-castable.");
            }
        }
        return $this;
    }

    function xmlCdata($str)
    {
        return $this->xmlText($str);
    }

    function xmlText($str)
    {
        if (! $this->xmlIsStringCastable($str)) {
            $this->_error = true;
            throw new DomainException('Text element must be string-castable.');
        }
        return $this;
    }

    function xmlComment($str)
    {
        if (! $this->xmlIsStringCastable($str)) {
            $this->_error = true;
            throw new DomainException('XML comment must be string-castable.');
        }

        if (false !== strpos($str, '--')) {
            $this->_error = true;
            throw new DomainException('XML comment must not contain "--".');
        }
        return $this;
    }

    function xmlPi($target, $data)
    {
        if (! $this->xmlIsStringCastable($target) || ! $this->xmlIsStringCastable($data)) {
            $this->_error = true;
            throw new DomainException('ProcessingInstruction must be string-castable.');
        }
        return $this;
    }

    function xmlRaw($xml)
    {
        return $this;
    }

    function xmlRender()
    {
        $remains = count($this->_stack);
        if ($remains > 0) {
            $this->_error = true;
            throw new DomainException('Opening and ending tag mismatch: ' . implode(',', $this->_stack));
        }

        return $this->_error ? 'ng' : 'ok';
    }

    function __toString() {
        return $this->xmlRender();
    }

    function xmlEcho()
    {
        echo $this;
        return $this;
    }

    protected static function xmlIsStringCastable($mixed)
    {
        $type = gettype($mixed);
        switch ($type) {
            case 'boolean': case 'integer': case 'double':
            case 'string': case 'NULL':
                return true;
            case 'object':
                if (method_exists($mixed, '__toString')) {
                    return true;
                }
                if ($mixed instanceof DateTime) {
                    return true;
                }
            default:
                return false;
        }
    }
}
