<?php

namespace App\Model\Manager;

use App\Model\Entity\Article;
use Connect;

class ArticleManager
{
    public const TABLE = 'mdf58_article';

    /**
     * @return array
     */
    public static function findAll(): array
    {
        $articles = [];
        $query = Connect::dbConnect()->query("SELECT * FROM " . self::TABLE);
        if ($query) {
            $userManager = new UserManager();
            foreach ($query->fetchAll() as $articleData) {
                $articles[] = (new Article())
                    ->setId($articleData['id'])
                    ->setAuthor(UserManager::getUserById($articleData['mdf58_user_fk']))
                    ->setContent($articleData['content'])
                    ->setTitle($articleData['title'])
                    ->setSummary($articleData['summary'])
                ;
            }
        }
        return $articles;
    }


    /**
     * @param Article $article
     * @param string $title
     * @param string $summary
     * @param string $content
     * @param int $id
     * @return bool
     */
    public static function addNewArticle(Article &$article, string $title,string $summary, string $content, int $id):bool
    {
        $stmt = Connect::dbConnect()->prepare("
            INSERT INTO " .self::TABLE . " (title,summary, content, mdf58_user_fk)
            VALUES (:title,:summary, :content, :mdf58_user_fk)
        ");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':summary', $summary);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':mdf58_user_fk', $id);

        $result = $stmt->execute();
        $article->setId(Connect::dbConnect()->lastInsertId());
        return $result;
    }
}