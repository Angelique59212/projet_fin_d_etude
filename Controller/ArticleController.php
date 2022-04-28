<?php

namespace App\Controller;

use AbstractController;


class ArticleController extends AbstractController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->render('article/dysorthographie');
    }

}