<?php

namespace App\Controller;

use AbstractController;

class ConfidentialityController extends AbstractController
{
    /**
     * @return void
     */
    public function index()
    {
        $this->render('confidentiality/confidentiality');
    }
}