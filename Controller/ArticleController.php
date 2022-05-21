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
        if (!self::verifyRole()) {
            header('Location: /index.php?c=home');
        }

        if ($this->verifyFormSubmit()) {
            $image = $this->getFormFieldImage('image');

            // Redirect if no image provided.
            if (!$image) {
                $_SESSION['error'] = "Vous n'avez pas fourni d'image";
                header('location: /index.php?c=article&a=add-article');
                die();
            }

            $user = $_SESSION['user'];

            // Getting and securing form content.
            $title = $this->dataClean($this->getFormField('title'));
            $summary = $this->dataCleanHtmlContent($this->getFormField('summary'));
            $content = $this->dataCleanHtmlContent($this->getFormField('content'));

            $article = new Article();
            $article
                ->setTitle($title)
                ->setSummary($summary)
                ->setImage($image)
                ->setContent($content)
                ->setAuthor($user)
            ;

            if (ArticleManager::addNewArticle($article)) {
                $_SESSION['success'] = "Votre article a bien été ajouté";
                header('Location: /index.php?c=home&a=home');
            }
        } else {
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
        $this->redirectIfConnected();
        if(!self::verifyRole()) {
            header('Location: /index.php?c=home');
        }
        if (ArticleManager::articleExists($id)) {
            $article = ArticleManager::getArticleById($id);
            ArticleManager::deleteArticle($article);
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
        $this->redirectIfNotConnected();
        if(!self::verifyRole()) {
            header('Location: /index.php?c=home');
        }

        if (isset($_POST['save']) && ArticleManager::articleExists($id)) {
            $title = $this->dataClean($this->getFormField('title'));
            $summary = $this->dataCleanHtmlContent($this->getFormField('summary'));
            $content = $this->dataCleanHtmlContent($this->getFormField('content'));
            $image = $this->getFormFieldImage('image');

            if (!$image) {
                $image = null;
            }

            ArticleManager::editArticle($id, $title, $summary, $content, $image);
            header('Location: /index.php?c=article&a=list-article');
        }
        $this->render('article/edit-article', [
            'article' => ArticleManager::getArticleById($id)
        ]);
    }
}