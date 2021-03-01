<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Item;
use App\Entity\Todolist;
use Datetime;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TodolistController extends BaseController
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(Todolist::class, $em);
    }

    /**
     * @Route("/todolists", name="todolists", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getTodolist(): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'description', 'category' => ['id', 'title']]];
        return $this->getEntity($neededData);
    }

    /**
     * @Route("/todolist/{id}", name="one_todolist", methods={"GET"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOneTodolist($id): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'description', 'category' => ['id', 'title']]];
        return $this->getOneEntity($id, $neededData);
    }

    /**
     * @Route("/todolist", name="todolist_create", methods={"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createItem(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = $request->getContent();
        $verif = json_decode($data);

        try {

            if (isset($verif->title) && !is_string($verif->title)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('Le titre doit etre une chaine de caracteres');
                return $response;
            }
            if (isset($verif->description) && !is_string($verif->description)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('La description doit etre une chaine de caracteres');
                return $response;
            }
            $entity = $serializer->deserialize($data, Todolist::class, 'json', ['groups' => 'todolist']);
            $entity->setCreatedAt(new Datetime());
            return $this->createEntity($entity);
        } catch (ErrorException $e) {
            $response = new JsonResponse();
            $response->setStatusCode(400);
            $response->setContent('Syntax Error');
            return $response;
        }
    }

    /**
     * @Route("/todolist/{id}", name="todolist_patch_update", methods={"PATCH"})
     * @param $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateTodolist($id, Request $request): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'description', 'category' => ['id', 'title']]];
        $entity = $this->getDoctrine()->getManager()->getRepository(Todolist::class)->find($id);
        $data = json_decode($request->getContent());
        $error = [];
        try {
            if (isset($data->title)) {
                if (!is_string($data->title)) {
                    $response = new JsonResponse();
                    $response->setStatusCode(400);
                    $response->setContent('Le titre doit etre une chaine de caracteres');
                    return $response;
                } else {
                    $entity->setTitle($data->title);
                }
            }
            if (isset($data->description)) {
                if (!is_string($data->description)) {
                    $response = new JsonResponse();
                    $response->setStatusCode(400);
                    $response->setContent('La description doit etre une chaine de caracteres');
                    return $response;
                } else {
                    $entity->setDescription($data->description);
                }
            }
            if (isset($data->itemId)) {
                if ($this->getDoctrine()->getManager()->getRepository(Item::class)->find($data->itemId) != null) {
                    $item = $this->getDoctrine()->getManager()->getRepository(Item::class)->find($data->itemId);
                    $entity->addItem($item);
                } else {
                    array_push($error, 'Item not found');
                }
            }
            if (isset($data->categoryId)) {
                if ($this->getDoctrine()->getManager()->getRepository(Category::class)->find($data->categoryId) != null) {
                    $category = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($data->categoryId);
                    $entity->setCategory($category);
                } else {
                    array_push($error, 'Category not found');
                }
            }
            if (!empty($error)) {
                $response = new JsonResponse();
                $response->setStatusCode(404);
                $response->setContent(implode(",", $error));
                return $response;
            }
            return $this->updateEntity($id, $entity, $neededData);
        } catch (NotEncodableValueException $e) {
            $response = new JsonResponse();
            $response->setStatusCode(400);
            $response->setContent('Syntax Error');
            return $response;
        }
    }

    /**
     * @Route("/todolist/{id}", name="todolist_delete", methods={"DELETE"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteItem($id): JsonResponse
    {
        return $this->deleteEntity($id);
    }
}
