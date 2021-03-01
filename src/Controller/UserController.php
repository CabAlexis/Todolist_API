<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends BaseController
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(User::class, $em);
    }

    /**
     * @Route("/users", name="users", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getUsers(): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','username', 'Items' => ['id', 'title']]];
        return $this->getEntity($neededData);
    }

    /**
     * @Route("/user/{id}", name="one_user", methods={"GET"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOneUser($id): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','username']];
        return $this->getOneEntity($id, $neededData);
    }

    /**
     * @Route("/user", name="user_create", methods={"POST"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function createUser(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','username']];
        $data = $request->getContent();
        $verif = json_decode($data);
        try {

            if (isset($verif->username) && !is_string($verif->username)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('Le username doit etre une chaine de caracteres');
                return $response;
            }


            $entity = $serializer->deserialize($data, User::class, 'json', $neededData);

            return $this->createEntity($entity);
        } catch (NotEncodableValueException $e) {
            $response = new JsonResponse();
            $response->setStatusCode(400);
            $response->setContent('Syntax Error');
            return $response;
        }
    }

    /**
     * @Route("/user/{id}", name="user_update", methods={"PATCH"})
     * @param $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateUser($id, Request $request): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title']];
        $entity = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);
        $data = json_decode($request->getContent());
        $error = [];
        try {
            if (isset($data->username) && !is_string($data->username)) {
                $response = new JsonResponse();
                $response->setStatusCode(400);
                $response->setContent('Le username doit etre une chaine de caracteres');
                return $response;
            }
            $entity->setUsername($data->username);
            if (isset($data->itemId)) {
                if ($this->getDoctrine()->getManager()->getRepository
                    (Item::class)->find($data->itemId) != null) {
                    $item = $this->getDoctrine()->getManager()->getRepository
                    (Item::class)->find($data->itemId);
                    $entity->addItem($item);
                } else {
                    array_push($error, 'Item not found');
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
     * @Route("/user/{id}/items", name="user_items", methods={"GET"})
     * @param $id
     * @param UserRepository $repo
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getItemsByUser($id, UserRepository $repo): JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title','status']];
        $data = $repo->listByUser($id);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($data, null, $neededData);
        return $this->json($selectedData);
    }

    /**
     * @Route("/user/{userId}/item/{itemId}", name="user_item",
     *     methods={"GET"})
     * @param $userId
     * @param $itemId
     * @param UserRepository $repo
     * @return JsonResponse
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function getOneItemByUser($userId, $itemId, UserRepository
    $repo):
    JsonResponse
    {
        $neededData = [AbstractNormalizer::ATTRIBUTES => ['id','title', 'status']];
        $data = $repo->oneItemByUser($userId, $itemId);
        $serializer = new Serializer([new ObjectNormalizer()]);
        $selectedData = $serializer->normalize($data, null, $neededData);
        return $this->json($selectedData);
    }

    /**
     * @Route("/user/{id}", name="user_delete", methods={"DELETE"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteUser($id): JsonResponse
    {
        return $this->deleteEntity($id);
    }
}
