<?php

namespace App\Controller;

use AbstractController;

class ArticleController extends AbstractController
{
    public function index()
    {
        $this->render('home/home');
    }
}