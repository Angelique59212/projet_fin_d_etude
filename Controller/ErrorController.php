<?php

namespace Controller\AbstractController;

use AbstractController;

class ErrorController extends AbstractController
{
    /**
     * Control the error page
     * @param $askPage
     * @return void
     */
    public function error404($askPage)
    {
        $this->render('error/404');
    }

    /**
     * @return void
     */
    public function index()
    {

    }
}