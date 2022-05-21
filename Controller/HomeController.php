<?php

namespace App\Controller;

use AbstractController;
use App\Model\Manager\ArticleManager;
use App\Model\Manager\UserManager;

class HomeController extends AbstractController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->render('home/home', [
            'articles' => ArticleManager::findAll(3),
        ]);
    }
}