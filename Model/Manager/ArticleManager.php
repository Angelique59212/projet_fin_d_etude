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
                    ->setImage($articleData['image'])
                ;
            }
        }
        return $articles;
    }

    /**
     * @param Article $article
     * @param string $title
     * @param string $summary
     * @param string $image
     * @param string $content
     * @param int $id
     * @return bool
     */
    public static function addNewArticle(Article &$article, string $title,string $summary,string $image, string $content, int $id):bool
    {
        $stmt = Connect::dbConnect()->prepare("
            INSERT INTO " .self::TABLE . " (title,summary,image,content, mdf58_user_fk)
            VALUES (:title,:summary,:image, :content, :mdf58_user_fk)
        ");

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':summary', $summary);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':mdf58_user_fk', $id);

        $result = $stmt->execute();
        $article->setId(Connect::dbConnect()->lastInsertId());
        return $result;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function articleExists(int $id): bool
    {
        $result = Connect::dbConnect()->query("SELECT count(*) as cnt FROM " . self::TABLE . " WHERE id = $id");
        return $result ? $result->fetch()['cnt'] : 0;
    }

    /**
     * @param int $id
     * @return Article|null
     */
    public static function getArticleById(int $id): ?Article
    {
        $result = Connect::dbConnect()->query("SELECT * FROM " . self::TABLE . " WHERE id = $id");
        return $result ? self::makeArticle($result->fetch()) : null;
    }

    /**
     * @param array $data
     * @return Article
     */
    private static function makeArticle(array $data): Article
    {
        return (new Article())
            ->setId($data['id'])
            ->setTitle($data['title'])
            ->setSummary($data['summary'])
            ->setImage($data['image'])
            ->setContent($data['content'])
            ->setAuthor(UserManager::getUserById($data['mdf58_user_fk']))
            ;
    }
}