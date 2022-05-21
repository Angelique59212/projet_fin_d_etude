<?php

namespace Controller;
use AbstractController;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../Controller/AbstractController.php';

class Purifier extends AbstractController
{

    public function index()
    {
        // TODO: Implement index() method.
    }
}

class AbstractControllerTest extends TestCase
{

    public function testHtmlPurifier()
    {
        $html = '
            <meta name="keywords" content="keyword1, keyword2, keyword3">
            <a href="something" onclick= "bad()">text</a> onclick not in tags
            <a href="something" onclick =bad()>text</a>
            <a href="something" onclick=bad(\'test\')>text</a>
            <a href="something" onclick=bad("test")>text</a>
            <a href="something" onclick="bad()" >text</a>
            <a href="http://mydomain.com/index.php?oninaval=12" class="titi">text</a>
            What if I write john+onelia=love forever?
            
                <a href="something" onclick="bad()">text</a> onclick not in tags
                <a href="something" onclick=bad()>text</a>
                <a href="something" onclick="bad()" >text</a>
            
            <a href="something" onclick=a++ >text</a>
            
                onclick="asd <span class="myclass"> not in tag too.</span>
            <!-- onclick=" --><a href="something" onclick= "bad()">text</a>
            <textarea><enter onclick="dothat()" text here></textarea>
                yoko ono="john lennon"
                    <img src="/images/img1.jpg" alt="onclick=thegood() onclick=thebad() "/>
            <img alt="onclick=" src=/images/theugly.jpg> the most important part of the message <p class="disappears"></p>
            
            <a href="" onmouseover=a=7>button1</a>
            <a href="something" onclick=a++>text</a>
            <a href="something" onclick=a<<1>text</a>
            <a href="" onmouseover="alert(a);">button2</a>
        ';

        $d = new Purifier();
        $result = $d->dataCleanHtmlContent($html);
        $this->assertStringNotContainsStringIgnoringCase('onclick=\"', $result);
    }
}