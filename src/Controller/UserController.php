<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends BaseController
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(User::class, $em);
    }

    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function getUsers(): JsonResponse
    {
        $groups = ['groups' => 'user'];
        return BaseController::getEntity($groups);
    }

    /**
     * @Route("/user/{id}", name="one_user", methods={"GET"})
     */
    public function getOneUser($id): JsonResponse
    {
        $groups = ['groups' => 'user'];
        return BaseController::getOneEntity($id, $groups);
    }

    /**
     * @Route("/user", name="user_create", methods={"POST"})
     */
    public function createUser(Request $request, SerializerInterface $serializer): JsonResponse
    {
        try {
            $data = $request->getContent();
            $verif = json_decode($data);


            if (isset($verif->username) && !is_string($verif->username)) {
                return $this->json([
                    'status' => 400,
                    'message' => 'Le username doit obligatoirement etre une chaine de caractere.'
                ]);
            }


            $entity = $serializer->deserialize($data, User::class, 'json', ['groups' => 'user']);

            return BaseController::createEntity($entity);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/user/{id}", name="user_update", methods={"PUT"})
     */
    public function updateUser($id, Request $request): JsonResponse
    {
        $groups = ['groups' => 'user'];
        $entity = $this->getDoctrine()->getManager()->getRepository(User::class)->find($id);

        try {
            $data = json_decode($request->getContent());

            if (isset($data->username) && !is_string($data->username)) {
                return $this->json([
                    'status' => 400,
                    'message' => 'Le username doit obligatoirement etre une chaine de caractere.'
                ]);
            }
            $entity->setUsername($data->username);
            return BaseController::updateEntity($id, $entity, $groups);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function deleteUser($id): JsonResponse
    {
        return BaseController::deleteEntity($id);
    }
}
