<?php

namespace App\Model\Manager;



use App\Model\Entity\Article;
use App\Model\Entity\Comment;
use App\Model\Entity\User;
use Connect;

class CommentManager
{
    public const TABLE = 'mdf58_comments';

    /**
     * @return array
     */
    public static function findAll(): array
    {
        $comments = [];

        $query = Connect::dbConnect()->query("SELECT * FROM " . self::TABLE . " ORDER BY id DESC");

        if ($query) {
            if (isset($_SESSION['user'])) {
                foreach ($query->fetchAll() as $comment) {
                    $comments[] = (new Comment())
                        ->setId($comment['id'])
                        ->setContent($comment['content'])
                        ->setAuthor(UserManager::getUserById($comment['mdf58_user_fk']))
                        ->setArticle(ArticleManager::getArticleById($comment['mdf58_article_fk']));
                }
            }
        }
        return $comments;
    }

    /**
     * verify comment exist
     * @param int $id
     * @return int|mixed
     */
    public static function commentExists(int $id)
    {
        $result = Connect::dbConnect()->query("SELECT count(*) as cnt FROM " . self::TABLE);
        return $result ? $result->fetch()['cnt'] : 0;
    }

    /**
     * @param string $content
     * @param int $user_fk
     * @param int $article_fk
     * @return void
     */
    public static function addComment(string $content,int $user_fk,int $article_fk)
    {
        $stmt = Connect::dbConnect()->prepare("
            INSERT INTO ". self::TABLE. " (content, mdf58_user_fk, mdf58_article_fk)
                VALUES ( :content, :mdf58_user_fk, :mdf58_article_fk)
        ");

        if (isset($_SESSION['user'])) {
            $user = $_SESSION ['user'];

            /* @var User $user */
            $user_fk = $user->getId();
        }

        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':mdf58_user_fk', $user_fk);
        $stmt->bindParam(':mdf58_article_fk', $article_fk);

        $stmt->execute();
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function deleteComment (int $id): bool
    {
            $query =  Connect::dbConnect()->exec("
            DELETE FROM " . self::TABLE . " WHERE id = $id
        ");
          if ($query) {
              return true;
          }
          else {
              return false;
          }
    }

    /**
     * retrieve a comment by its id
     * @param Article $article
     * @return array
     */
    public static function getCommentByArticle(Article $article):array
    {
        $comments = [];
        $query = Connect::dbConnect()->query("
            SELECT *FROM " . self::TABLE . " WHERE mdf58_article_fk = " . $article->getId() ." ORDER BY id DESC
        ");

        if ($query) {
            foreach ($query->fetchAll() as $commentData) {
                $comments[] = (new Comment())
                    ->setId($commentData['id'])
                    ->setContent($commentData['content'])
                    ->setAuthor(UserManager::getUserById($commentData['mdf58_user_fk']))
                    ->setArticle(ArticleManager::getArticleById($commentData['mdf58_article_fk']));
            }
        }
        return $comments;
    }
}