<?php

use jerryhsia\readability\ReadabilityParser;
use jerryhsia\readability\RegexParser;

require_once __DIR__.'/vendor/autoload.php';

if ($_POST && array_key_exists('url', $_POST) && $_POST['url']) {
    $source = file_get_contents($_POST['url']);
    $parserType = array_key_exists('parser', $_POST) ? $_POST['parser'] : 'ReadabilityParser';
    if ($parserType == 'ReadabilityParser') {
        $parser = new ReadabilityParser($source);
    } else {
        $selector = array_key_exists('selector', $_POST) ? $_POST['selector'] : null;
        $parser = new RegexParser($source, [
            'contentSelector' => $selector
        ]);
    }

    var_dump($parser->getTitle(), $parser->getCover(), $parser->getContent(), $parser->getContentArray());
} else {
    echo <<<HTML
<html>
    <head>
        <meta charset="utf-8">
        <style>
            body {
                text-align: center;
                padding-top: 100px;
            }

            .input {
                width: 500px;
                height: 30px;
                font-size: 18px;
            }

            .submit {
                margin-top: 15px;
                width: 100px;
                height: 30px;
            }
        </style>
    </head>
    <body>
        <form method="post" action="">
            URL：<input class="input" type="text" name="url" placeholder="Input url.."><br>
            Parser：<input type="radio" name="parser" value="ReadabilityParser" checked> ReadabilityParser
            <input type="radio" name="parser" value="RegexParser"> RegexParser<br>
            Content Selector：<input type="text" name="selector" placeholder="Required by RegexParser"><br>
            <input class="submit" type="submit" value="Submit">
        </form>
    </body>
</html>
HTML;
}

