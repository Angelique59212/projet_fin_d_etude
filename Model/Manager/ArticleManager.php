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
            $format = 'Y-m-d H:i:s';

            foreach ($query->fetchAll() as $articleData) {
                $articles[] = (new Article())
                    ->setId($articleData['id'])
                    ->setAuthor(UserManager::getUserById($articleData['mdf58_user_fk']))
                    ->setContent($articleData['content'])
                    ->setTitle($articleData['title'])
                    ->setDateAdd($articleData['date_add'])
                    ->setDateUpdate($articleData['date_update'])
                ;
            }
        }
        return $articles;
    }


    /**
     * @param Article $article
     * @param string $title
     * @param string $content
     * @param int $id
     * @return bool
     */
    public static function addNewArticle(Article &$article, string $title, string $content, int $id):bool
    {
        $stmt = Connect::dbConnect()->prepare("
            INSERT INTO " .self::TABLE . " (title, content, mdf58_user_fk)
            VALUES (:title, :content, :mdf58_user_fk)
        ");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':mdf58_user_fk', $id);

        $result = $stmt->execute();
        $article->setId(Connect::dbConnect()->lastInsertId());
        return $result;
    }
}