<?php
/**
 *
 *
 */
if (!class_exists('XML_Builder_Interface',false)) require_once dirname(__FILE__).'/Interface.php';
abstract class XML_Builder_Abstract implements XML_Builder_Interface
{
    final function __get($name)
    {
        return $this->$name();
    }

    final function __call($method, $args)
    {
        $sigil = XML_Builder::$sigil;
        $sigilLength = strlen($sigil);

        if ($sigil === $method) {
            return $this->xmlEnd();
        }

        $prefix = substr($method, 0, $sigilLength);
        if ($prefix === $sigil) {
            $methodName = 'xml' . substr($method, $sigilLength);
            return call_user_func_array(array($this, $methodName), $args);
        }

        $immediately = false;
        $postfix = substr($method, strlen($method) - $sigilLength);
        if ($postfix === $sigil) {
            $immediately = true; //即座にエレメントを閉じて返却するフラグon
            $method = substr($method, 0, -$sigilLength);
        }

        $elem = $this->xmlElem($method);
        foreach ($args as $param) {
            if (is_array($param)) {
                $elem->xmlAttr($param);
            } else {
                $elem->xmlText($param);
            }
        }

        if ($immediately) {
            return $elem->xmlEnd();
        } else {
            return $elem;
        }
    }

    final function xmlDo($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('bad callback');
        }
        call_user_func($callback, $this);
        return $this;
    }

    final function xmlExport(&$out)
    {
        $out = $this;
        return $this;
    }

    final function xmlPause(&$out)
    {
        $out = $this;
    }
}
