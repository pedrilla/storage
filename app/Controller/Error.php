<?php

namespace App\Controller;

use Light\ErrorController;

class Error extends ErrorController
{
    public function index()
    {
        $this->getResponse()->setStatusCode(200);

        return [
            'error' => true,
            'message' => $this->getException()->getMessage()
        ];
    }
}