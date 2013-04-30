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
    protected $_stack = array(), $_error=false;

    function xmlElem($name)
    {
        if (! $this->xmlIsStringCastable($name)) {
            $this->_error = true;
            trigger_error('$name must be string-castable.', E_USER_WARNING);
        }
        array_push($this->_stack, $name);
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
                trigger_error("\$name($name) must be string-castable.", E_USER_WARNING);
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
            trigger_error('$name must be string-castable.', E_USER_WARNING);
        }
        return $this;
    }

    function xmlComment($str)
    {
        if (! $this->xmlIsStringCastable($str)) {
            $this->_error = true;
            trigger_error('XML comment must be string-castable.', E_USER_WARNING);
        }

        if (preg_match('/--/', $str)) {
            $this->_error = true;
            trigger_error('XML comment must not have "--"', E_USER_WARNING);
        }
        return $this;
    }

    function xmlPi($target, $data)
    {
        if (! $this->xmlIsStringCastable($target) || ! $this->xmlIsStringCastable($data)) {
            $this->_error = true;
            trigger_error('ProcessingInstruction must be string-castable.', E_USER_WARNING);
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
            throw new LogicException('Opening and ending tag mismatch: ' . implode(',', $this->_stack));
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
        }

        return false;
    }
}
