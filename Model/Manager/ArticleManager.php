<?php

namespace App\Model\Manager;

use App\Model\Entity\Article;
use Connect;

class ArticleManager
{
    public const TABLE = 'mdf58_article';

    /**
     * @param int|null $limit
     * @return array
     */
    public static function findAll(int $limit = null): array
    {
        $articles = [];
        $limitQuery = $limit !== null ? " LIMIT $limit" : '';
        $query = Connect::dbConnect()->query("SELECT * FROM " . self::TABLE . " ORDER BY id DESC" . $limitQuery);
        if ($query) {
            foreach ($query->fetchAll() as $articleData) {
                $articles[] = (new Article())
                    ->setId($articleData['id'])
                    ->setAuthor(UserManager::getUserById($articleData['user_fk']))
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
     * @return bool
     */
    public static function addNewArticle(Article &$article):bool
    {
        $stmt = Connect::dbConnect()->prepare("
            INSERT INTO " .self::TABLE . " (title,summary,image,content, user_fk)
            VALUES (:title,:summary,:image, :content, :user_fk)
        ");

        $stmt->bindValue(':title', $article->getTitle());
        $stmt->bindValue(':summary', $article->getSummary());
        $stmt->bindValue(':image', $article->getImage());
        $stmt->bindValue(':content', $article->getContent());
        $stmt->bindValue(':user_fk', $article->getAuthor()->getId());

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
            ->setAuthor(UserManager::getUserById($data['user_fk']))
            ;
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $summary
     * @param string $content
     * @param string|null $image
     * @return void
     */
    public static function editArticle(int $id, string $title,string $summary, string $content, string $image = null)
    {
        $imageSql = $image ? ", image = :image" : '';
        $stmt = Connect::dbConnect()->prepare("
            UPDATE " . self::TABLE . " SET title = :title,summary = :summary, content = :content" . $imageSql . " WHERE id = :id
        ");

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':summary', $summary);
        $stmt->bindParam(':content', $content);
        if($image) {
            $stmt->bindParam(':image', $image);
        }
        $stmt->execute();
    }

    /**
     * @param Article|null $article
     * @return false|int
     */
    public static function deleteArticle(?Article $article)
    {
        if (self::articleExists($article->getId())) {
            return Connect::dbConnect()->exec("
                DELETE FROM " . self::TABLE . " WHERE id = {$article->getId()}
            ");
        }
        return false;
    }
}