<?php

namespace App\Controller;



use AbstractController;
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
        self::redirectIfNotConnected();

        if($this->verifyFormSubmit()) {
            $userSession = $_SESSION['user'];
            /* @var User $userSession */
            $user =$userSession->getId();

            $content = $this->dataClean($this->getFormField('content'));

            CommentManager::addComment($content,$user,$id);
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
     * @param int $id
     * @return void
     */
    public function deleteComment(int $id) {
        if (CommentManager::commentExists($id)) {
            if (CommentManager::deleteComment($id)) {
                header('Location: /index.php?c=article&a=list-article');
            }
            else {
                header('Location: /index.php?c=home&a=index');
            }
        }
          $this->index();
        }
}