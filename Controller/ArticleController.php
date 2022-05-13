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

    /**
     * @return void
     */
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
            $image = $this->getFormFieldImage('image');
            if (!$image) {
                header('location: /index.php?c=article&a=add-article');
                die();
            }
            $content = $this->getFormField('content');

            $article = new Article();
            $article
                ->setTitle($title)
                ->setSummary($summary)
                ->setImage($image)
                ->setContent($content)
                ->setAuthor($user)
            ;

            if (ArticleManager::addNewArticle($article, $title,$summary,$image, $content, $_SESSION['user']->getId())) {
                $_SESSION['error'] = "votre article as bien etait ajouté";
                header('Location: /index.php?c=home&a=home');
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
            'article' => ArticleManager::findAll(),
        ]);
    }

    /**
     * @param int|null $id
     * @return void
     */
    public function showArticle(int $id = null)
    {
        if (null === $id) {
            header("Location: /index.php?c=home");
        }
        if (ArticleManager::articleExists($id)) {
            $this->render('article/show-article', [
                "article" => ArticleManager::getArticleById($id),
            ]);
        } else {
            $this->index();
        }
    }

    /**
     * @param int $id
     * @return void
     */
    public function deleteArticle(int $id) {
        if (ArticleManager::articleExists($id)) {
            $article = ArticleManager::getArticleById($id);
            $deleted = ArticleManager::deleteArticle($article);
            header('Location: /index.php?c=article&a=list-article');
        }
        $this->index();
    }

    /**
     * @param int $id
     * @return void
     */
    public function editArticle(int $id)
    {
        if (isset($_POST['save'])) {
            if (ArticleManager::articleExists($id)) {
                $title = $this->dataClean($this->getFormField('title'));
                $summary = $this->dataClean($this->getFormField('summary'));
                $content = $this->dataClean($this->getFormField('content'));

                ArticleManager::editArticle($id, $title, $summary, $content);
                header('Location: /index.php?c=article&a=list-article');
            }
        }
        $this->render('article/edit-article', [
            'article'=>ArticleManager::getArticleById($id)
        ]);
    }
}