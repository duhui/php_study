<?php

namespace MDPI\RefBundle\Service;


class ParseXmlService
{
    public function xmlToHash($node)
    {
        $thisNode = array();

        foreach($node->attributes as $attr) {
            $thisNode["@" . $attr->name] = $attr->value;
        }

        foreach($node->childNodes as $child){
            $key = $this->getKey($child);
            $value = $this->getValue($child);
            if ($value) {
                $thisNode[$key] = $value;
            }
        }
        return $thisNode;
    }

    public function getValue($node)
    {
        if ($node->nodeType === XML_ELEMENT_NODE){
            return $this->xmlToHash($node);
        } elseif ($node->nodeType === XML_TEXT_NODE) {
            $value = $node->nodeValue;
            if (preg_match("/^\n+\s*$/", $value)) {
                return null;
            }
            return $value;
        }
    }

    public function getKey($node)
    {
        $path = explode("/", $node->getNodePath());
        return str_replace("()", "", array_pop($path));
    }
}
