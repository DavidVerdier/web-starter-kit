<?php

namespace App\Controller;

use App\Entity\Test;
use App\Kernel\Controller\AbstractController;

class DefaultController extends AbstractController
{
    public function list()
    {
        $em = $this->getDoctrine();

        $tests = $em->getRepository(Test::class)->findAll();

        dump($tests);die;

        $test = new Test();

        $em->persist($test);
        $em->flush();


        dump($this->getDoctrine()->getConnection());
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
