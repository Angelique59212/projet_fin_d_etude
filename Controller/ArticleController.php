<?php

namespace App\Controller;

use AbstractController;
use App\Model\Entity\Article;
use App\Model\Entity\User;
use App\Model\Manager\ArticleManager;
use App\Model\Manager\UserManager;


class ArticleController extends AbstractController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->render('home/home');
    }

    /**
     * redirect when clicked on read more
     * @param $action
     * @return void
     */
    public function page($action)
    {
       $this->render('article/' . $action);
    }

    public function addArticle()
    {
        self::redirectIfNotConnected();
        self::verifyRole();
        if (!self::verifyRole()) {
            header('Location: /index.php?c=home');
        }

        if ($this->verifyFormSubmit()) {
            $userSession = $_SESSION['user'];
            /* @var User $userSession */
            $user = UserManager::getUserById($userSession->getId());

            $title = $this->dataClean($this->getFormField('title'));
            $summary = $this->dataClean($this->getFormField('summary'));
            $content = $this->dataClean($this->getFormField('content'));

            $article = new Article();
            $article
                ->setTitle($title)
                ->setSummary($summary)
                ->setContent($content)
                ->setAuthor($user)
            ;

            if (ArticleManager::addNewArticle($article, $title,$summary, $content, $_SESSION['user']->getId())) {
                $this->render('article/list-article');
            }
        }else {
            $this->render('article/add-article');
        }
    }

    /**
     * @return void
     */
    public function listArticle() {
        $this->render('article/list-article', [
            'articles' => ArticleManager::findAll(),
        ]);
    }


}