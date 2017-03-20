<?php
use jerryhsia\readability\ReadabilityParser;
use jerryhsia\readability\RegexParser;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../vendor/autoload.php';

class ParserTest extends TestCase
{
    public $url = 'http://news.qq.com/a/20170320/018790.htm';

    public function testReadabilityParser()
    {
        $parser = new ReadabilityParser($this->url);
        var_dump($parser->getTitle());
        var_dump($parser->getCover());
        var_dump($parser->getContent());
        var_dump($parser->getContentArray());
    }

    public function testRegexParser()
    {
        $parser = new RegexParser($this->url, [
            'contentSelector' => '#Cnt-Main-Article-QQ'
        ]);
        var_dump($parser->getTitle());
        var_dump($parser->getCover());
        var_dump($parser->getContent());
        var_dump($parser->getContentArray());
    }
}
