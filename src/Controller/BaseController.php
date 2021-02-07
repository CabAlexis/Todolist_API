<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function getEntity(): JsonResponse
    {   
        $data = $this->getDoctrine()->getManager()->getRepository($this->entity)->findAll();
        return $this->json($data);
    }

    public function getOneEntity($id): JsonResponse
    {
        $entity = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        return $this->json($entity);
    }

    public function createEntity($entity): JsonResponse
    {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->json($entity, 201);
    }

    public function updateEntity($id, $entity): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        return $this->json($entity);
    }

    public function deleteEntity($id): JsonResponse
    {
        $entity = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        return new JsonResponse('Deleted', '200');
    }
}
