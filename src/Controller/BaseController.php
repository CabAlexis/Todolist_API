<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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

    protected function getEntity($neededData): JsonResponse
    {   
        $data = $this->getDoctrine()->getManager()->getRepository($this->entity)->findAll();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($data, null, $neededData);
        return $this->json($selectedData, 200);
    }

    protected function getOneEntity($id, $neededData): JsonResponse
    {
        $entity = $this->getDoctrine()->getManager()->getRepository($this->entity)->find($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($entity, null, $neededData);
        return $this->json($selectedData, 200);
    }

    protected function createEntity($entity): JsonResponse
    {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->json($entity, 201);
    }

    protected function updateEntity($id, $entity, $neededData): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($entity, null, $neededData);
        $em->persist($entity);
        $em->flush();
        return $this->json($selectedData, 200);
    }

    protected function deleteEntity($id): JsonResponse
    {
        $entity = $this->getDoctrine()->getManager()->getRepository($this->entity)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        return new JsonResponse('Deleted', '200');
    }
}
