<?php

namespace App\Controller;

use AbstractController;

class HomeController extends AbstractController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->render('home/home');
    }
}