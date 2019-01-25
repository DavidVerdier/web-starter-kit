<?php

namespace App\Controller;

use App\Kernel\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function list()
    {
        //return $this->redirect('http://jkfh');
        return $this->render('index.html.twig');
    }

    public function show($id)
    {
        dump($id);die;

        die('list ok');
    }
}
