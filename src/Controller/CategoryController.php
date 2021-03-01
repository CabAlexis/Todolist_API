<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends BaseController
{

    public function __construct(EntityManagerInterface $em)
    {
       parent::__construct(Category::class, $em);
    }

    /**
     * @Route("/categories", name="categories", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title']];
        return $this->getEntity($neededData);
    }

    /**
     * @Route("/category/{id}", name="one_category", methods={"GET"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOneCategory($id): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title']];
        return $this->getOneEntity($id, $neededData);
    }

    /**
     * @Route("/category", name="category_create", methods={"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createCategory(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title']];
        $data = $request->getContent();

        $verif = json_decode($data);
        try {
            if (isset($verif->title) && !is_string($verif->title)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('Le titre doit etre une chaine de caracteres');
                return $response;
            }
            $entity = $serializer->deserialize($data, Category::class, 'json', $neededData);

            return $this->createEntity($entity);
        }catch(NotEncodableValueException $e){
            $response = new JsonResponse();
            $response->setStatusCode(400);
            $response->setContent('Syntax Error');
            return $response;
        }
    }

    /**
     * @Route("/category/{id}", name="category_update", methods={"PATCH"})
     * @param $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateCategory($id, Request $request): JsonResponse
    {

        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title']];
        $entity = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        $data = json_decode($request->getContent());

        try {


            if (isset($data->title) && !is_string($data->title)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('Le titre doit etre une chaine de caracteres');
                return $response;
            }
            $entity->setTitle($data->title);
            return $this->updateEntity($id, $entity, $neededData);
        }catch(NotEncodableValueException $e){
            $response = new JsonResponse();
            $response->setStatusCode(400);
            $response->setContent('Syntax Error');
            return $response;
        } 
    }

    /**
     * @Route("/todolist/{id}/category", name="todolist_category",
     *     methods={"GET"})
     * @param $id
     * @param CategoryRepository $repo
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getByTodolist($id, CategoryRepository $repo): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title']];
        $data = $repo->listByTodolist($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($data, null, $neededData);
        return $this->json($selectedData);
    }

    /**
     * @Route("/todolist/{todolistId}/item/{itemId}", name="todolist_items",
     *     methods={"GET"})
     * @param $todolistId
     * @param $itemId
     * @param ItemRepository $repo
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getOneItemByTodolist($todolistId, $itemId, ItemRepository
    $repo):
    JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title','status']];
        $data = $repo->oneItemByTodolist($todolistId, $itemId);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($data, null, $neededData);
        return $this->json($selectedData);
    }

    /**
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteCategory($id): JsonResponse
    {
        return $this->deleteEntity($id);
    }
}
