<?php

namespace App\Controller;

use Light\Controller;
use Light\Cookie;
use Light\Front;

abstract class Base extends Controller
{
    /**
     * @throws \Exception
     */
    public function init()
    {
        parent::init();

        if (Front::getInstance()->getConfig()['key'] == $this->getParam('key')) {
            Cookie::set('authorized', true);
        }

        if (!Cookie::get('authorized')) {
            throw new \Exception('not authorized');
        }
    }
}