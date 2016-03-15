<?php

namespace BlueSteel42\SettingsBundle\Adapter;


/**
 * @author Umberto Stefani <umbe81@gmail.com>
 * @author Tonio Carta <plutonio21@gmail.com>
 */

class XmlAdapter extends AbstractBaseFileAdapter
{

    /**
     * @inheritdoc
     */
    protected function doGetValues()
    {
        $contents = $this->getFileContents();

        $dom = new \DOMDocument();
        $dom->loadXML($contents);
        $root = $dom->getElementsByTagName('root')->item(0);

        return $this->loadSection($root);
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $dom = new \DOMDocument();
        $root = $dom->createElement('root');
        $this->createChildren($root, $this->getValues());
        $dom->appendChild($root);

        $this->setFileContents($dom->saveXML());

        return $this;
    }

    /**
     * @param \DOMElement $node
     * @param array $children
     */
    protected function createChildren(\DOMElement $node, array $children)
    {
        foreach ($children as $childName => $childValue) {
            if (is_array($childValue)) {
                $child = $node->appendChild(new \DOMElement($childName));
                $this->createChildren($child, $childValue);
            } else {
                $node->appendChild(new \DOMElement($childName, $childValue));
            }
        }
    }

    /**
     * @param \DOMElement $node
     * @return array
     */
    protected function loadSection(\DOMElement $node)
    {
        $values = array();

        foreach ($node->childNodes as $childNode) {
            if ($childNode->hasChildNodes()) {
                if ($childNode->childNodes->length == 1 && $childNode->childNodes->item(0) instanceof \DOMText) {
                    $values[$childNode->nodeName] = $childNode->nodeValue;
                } else {
                    $values[$childNode->nodeName] = $this->loadSection($childNode);
                }
            }
        }
        return $values;
    }

    /**
     * @inheritdoc
     */
    protected function getFileContents()
    {
        $contents = parent::getFileContents();
        if ('' === $contents) {
            $contents = '<root></root>';
        }
        return $contents;
    }

    /**
     * @inheritdoc
     */
    protected function getFileName()
    {
        return 'bluesteel42_settings.xml';
    }
}