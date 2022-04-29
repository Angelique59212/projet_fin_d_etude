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
        $this->render('home/home');
    }

    public function page($action){
       $this->render('article/' . $action);
    }

}