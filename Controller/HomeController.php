<?php

namespace Controller\AbstractController;

class HomeController extends \AbstractController
{
    public function index()
    {
        $this->render('home/home');
    }
}