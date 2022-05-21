<?php

namespace App\Controller;

use AbstractController;
use App\Model\Entity\Comment;
use App\Model\Entity\User;
use App\Model\Manager\ArticleManager;
use App\Model\Manager\CommentManager;

class CommentController extends AbstractController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->render('home/home');
    }

    /**
     * @param int $id
     * @return void
     */
    public function addComment(int $id)
    {
        if(self::verifyUserConnect() === false) {
            $_SESSION['error'] = "Vous devez être connecté";
            self::redirectIfNotConnected();
        }

        if($this->verifyFormSubmit()) {
            $userSession = $_SESSION['user'];
            $user = $userSession->getId();
            $content = $this->dataClean($this->getFormField('content'));

            CommentManager::addComment($content, $user, $id);
            header('Location: /index.php?c=article&a=show-article&id='.$id);
        }

        $this->render('comment/add-comment',[
            'article'=>ArticleManager::getArticleById($id)
        ]);
    }

    /**
     * all comments
     * @return void
     */
    public function listComment() {
        $this->render('comment/list-comment', [
            'comment' => CommentManager::findAll(),
        ]);
    }

    /**
     * Delete a comment.
     * @param int $id
     * @return void
     */
    public function deleteComment(int $id) {
        $this->redirectIfNotConnected();
        $user = $_SESSION['user'];

        if (CommentManager::commentExists($id)) {
            $comment = CommentManager::getComment($id);
            // If user is admin or user is the comment author, then canDelete is true.
            $canDelete = self::verifyRole() || $comment->getAuthor()->getId() === $user->getId();
            if ($comment && $canDelete && CommentManager::deleteComment($id)) {
                (new ArticleController())->showArticle($comment->getArticle()->getId());
            }
        }

        (new ArticleController())->index();
    }
}