<?php

namespace App\Controller;

use AbstractController;

class Confidentiality extends AbstractController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->render('confidentiality/confidentiality');
    }
}