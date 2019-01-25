<?php

namespace App\Controller;

use App\Kernel\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function list()
    {
        //return $this->redirect('http://jkfh');
        return $this->render('default/index.html.twig');
    }

    public function show($id)
    {
        return $this->render('default/index.html.twig', array(
            'id' => $id
        ));
    }
}
