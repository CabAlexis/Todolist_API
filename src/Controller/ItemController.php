<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Todolist;
use App\Entity\User;
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

class ItemController extends BaseController
{
    public function __construct(EntityManagerInterface $em)
    {
       parent::__construct(Item::class, $em);
    }

    /**
     * @Route("/items", name="items", methods={"GET"})
     * @return JsonResponse
     */
    public function getItems(): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'status','todolist' => ['id', 'title'], 'userItem' => ['id', 'username']]];
        return $this->getEntity($neededData);
    }

    /**
     * @Route("/item/{id}", name="one_item", methods={"GET"})
     * @param $id
     * @return JsonResponse
     */
    public function getOneItem($id): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'status','todolist' => ['id', 'title'], 'userItem' => ['id', 'username']]];
        return $this->getOneEntity($id, $neededData);
    }

    /**
     * @Route("/item", name="item_create", methods={"POST"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function createItem(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'status','todolist' => ['id', 'title'], 'userItem' => ['id', 'username']]];
        $data = $request->getContent();
        $verif = json_decode($data);

        try {

            if (isset($verif->title) && !is_string($verif->title)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('Le titre doit etre une chaine de caracteres');
                return $response;
            }

            $entity = $serializer->deserialize($data, Item::class, 'json', $neededData);

            $entity->setStatus(false);

            return $this->createEntity($entity);
        }catch(NotEncodableValueException $e){
            $response = new JsonResponse();
            $response->setStatusCode(400);
            $response->setContent('Syntax Error');
            return $response;
        }
    }

    /**
     * @Route("/item/{id}", name="item_patch_update", methods={"PATCH"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function patchItem($id, Request $request): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'status','todolist' => ['id', 'title'], 'userItem' => ['id', 'username']]];
        $entity = $this->getDoctrine()->getManager()->getRepository(Item::class)->find($id);
        $data = json_decode($request->getContent());
        $error = [];
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
        if (isset($data->status)) {
            if (!is_bool($data->status)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('Le statut doit etre un boolean');
                return $response;
            } else {
                $entity->setStatus($data->status);
            }
        }
        if (isset($data->userId)) {
            if ($this->getDoctrine()->getManager()->getRepository(User::class)->find($data->userId) != null) {
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($data->userId);
                $entity->setUserItem($user);
            } else {
                array_push($error, 'User not found');
            }
        }
        if (isset($data->todolistId)) {
            if ($this->getDoctrine()->getManager()->getRepository(Todolist::class)->find($data->todolistId) != null) {
                $todolist = $this->getDoctrine()->getManager()->getRepository(Todolist::class)->find($data->todolistId);
                $entity->setTodolist($todolist);
            } else {
                array_push($error, 'Todolist not found');
            }
        }
        if (!empty($error)) {
            $response = new JsonResponse();
            $response->setStatusCode(404);
            $response->setContent(implode(",", $error));
            return $response;
        }

        return $this->updateEntity($id, $entity, $neededData);
    }

    /**
     * @Route("/todolist/{todolistId}/items", name="todolist_items",
     *     methods={"GET"})
     * @param $todolistId
     * @param ItemRepository $repo
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getItemsByTodolist($todolistId, ItemRepository $repo):
    JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title','status']];
        $data = $repo->listByTodolist($todolistId);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($data, null, $neededData);
        return $this->json($selectedData);
    }

    /**
     * @Route("/todolist/{todolistId}/item/{itemId}", name="todolist_item",
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
     * @Route("/item/{id}", name="item_delete", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function deleteItem($id): JsonResponse
    {
        return $this->deleteEntity($id);
    }
}
