<?php
namespace jerryhsia\readability;
use jerryhsia\readability\BaseParser;

class RegexParser extends BaseParser
{
    public $contentSelector = null;

    public function init()
    {
        if ($this->contentSelector === null) {
            throw new \Exception('Content selector required.');
        }
    }

    public function getContent()
    {
        $dom = str_get_html($this->getSource());
        /**
         * @var $node \simple_html_dom_node
         */
        $node = $dom->find($this->contentSelector, 0);

        return $node->outertext();
    }
}
