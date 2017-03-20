<?php
namespace jerryhsia\readability;

abstract class BaseParser
{
    public final function __construct($urlOrHtml, $config = [])
    {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }

        if (substr($urlOrHtml, 0, 4) == 'http') {
            $this->_source = file_get_contents($urlOrHtml);
        } else {
            $this->_source = $urlOrHtml;
        }

        preg_match("/charset=([\w|\-]+);?/", $this->_source, $match);
        $charset = isset($match[1]) ? $match[1] : 'utf-8';
        $charset = strtolower($charset);
        if (substr($charset, 0, 2) == 'gb') {
            $this->_source = iconv('gbk', 'utf-8', $this->_source);
        }

        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    protected $_source = null;

    public function getSource()
    {
        return $this->_source;
    }

    public function getCover()
    {
        if ($this->getContentNode()) {
            /**
             * @var $imageNode \simple_html_dom_node
             */
            $imageNode = $this->getContentNode()->find("img", 0);
            if ($imageNode) {
                $image = $imageNode->getAttribute("src");
                if (empty($image)) {
                    $image = $imageNode->getAttribute('data-src');
                }
                return $image;
            }
        }

        return null;
    }

    public function getTitle()
    {
        preg_match('/<title>(.*?)<\/title>/', $this->getSource(), $match);
        if (is_array($match) && isset($match[1])) {
            $split_point = ' - ';
            $title = trim($match[1]);
            $result = array_map('strrev', explode($split_point, strrev($title)));
            return sizeof($result) > 1 ? array_pop($result) : $title;
        }
        return null;
    }

    /**
     * @return string
     */
    abstract function getContent();

    /**
     * @var \simple_html_dom
     */
    protected $_contentNode = false;

    public function getContentNode()
    {
        if ($this->_contentNode === false) {
            $this->_contentNode = str_get_html($this->getContent());
        }
        return $this->_contentNode;
    }

    public function getContentArray()
    {
        $node = $this->getContentNode();
        if ($node) {
            return $this->getItems($node);
        }

        return [];
    }

    protected function getItems($node)
    {
        $items = [];
        foreach ($node->childNodes() as $n) {
            /**
             * @var $n \simple_html_dom_node
             */
            if ($n->tag == 'p') {
                $text = trim($n->innertext());
                if ($text == '<br>' || empty($text)) {
                    continue;
                }

                $images = [];

                $text = preg_replace_callback('/<img.*?src="(.*?)".*?>/', function($match) use (&$images) {
                    $images[] = $match[1];
                    return '|#|__IMAGE__|#|';
                }, $text);
                $text = strip_tags($text);

                $i = 0;
                foreach (explode('|#|', $text) as $string) {
                    $string = trim($string);
                    if (empty($string)) {
                        continue;
                    }

                    if ($string == '__IMAGE__') {
                        $items[] = [
                            'type' => 'image',
                            'value' => $images[$i++]
                        ];
                    } else {
                        $items[] = [
                            'type' => 'text',
                            'value' => trim($string)
                        ];
                    }
                }
            } else {
                $arr = $this->getItems($n);
                $items = array_merge($items, $arr);
            }
        }

        return $items;
    }
}
