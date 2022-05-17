<?php
require __DIR__ . '/../../../Model/Entity/AbstractEntity.php';
require __DIR__ . '/../../../Model/Entity/Article.php';

use App\Model\Entity\Article;
use PHPUnit\Framework\TestCase;

class ArticleTest extends TestCase
{
    /**
     * Test all Article entity getters are working.
     * @return void
     */
    public function testSettersGetters():void
    {
        $article = new Article();
        $article->setImage('fake.png');
        $this->assertEquals('fake.png', $article->getImage());
        $this->assertIsString($article->getImage());
    }
}