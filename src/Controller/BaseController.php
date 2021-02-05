<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController implements InterfaceController
{
    protected $entity;
    private $em;
    private $data;

    public function __construct($entity, $em)
    {
        $this->entity = $entity;
        $this->em = $em;
    }

    public function getEntity(): Response
    {   
        $data = $this->getDoctrine()->getManager()->getRepository($this->entity)->findAll();
        return $this->json($data);
    }

    public function getOneEntity($arg): Response
    {
        return $this->json();
    }

    public function createEntity(Request $request): Response
    {
        return $this->json();
    }

    public function updateEntity($arg, Request $request): Response
    {
        return $this->json();
    }

    public function deleteEntity($arg): Response
    {
        return $this->json();
    }
}
