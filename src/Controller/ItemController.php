<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Todolist;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;

class ItemController extends BaseController
{
    public function __construct(EntityManagerInterface $em)
    {
       parent::__construct(Item::class, $em);
    }

    /**
     * @Route("/items", name="items", methods={"GET"})
     */
    public function getItems(): JsonResponse
    {
        $groups = ['groups' => 'item'];
        return BaseController::getEntity($groups);
    }

    /**
     * @Route("/item/{id}", name="one_item", methods={"GET"})
     */
    public function getOneItem($id): JsonResponse
    {
        $groups = ['groups' => 'item'];
        return BaseController::getOneEntity($id, $groups);
    }

    /**
     * @Route("/item", name="item_create", methods={"POST"})
     */
    public function createItem(Request $request, SerializerInterface $serializer): JsonResponse
    {
        try {
            $data = $request->getContent();

            $verif = json_decode($data);

            if(isset($verif->title) && !is_string($verif->title)){
                return $this->json([
                    'status' => 400,
                    'message' => 'Le titre doit obligatoirement etre une chaine de caractere.'
                ]);
            }

            $entity = $serializer->deserialize($data, Item::class, 'json', ['groups' => 'item']);

            $entity->setStatus(false);

            return BaseController::createEntity($entity);
        }catch(NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/item/{id}", name="item_update", methods={"PUT"})
     */
    public function updateCategory($id, Request $request): JsonResponse
    {

        $groups = ['groups' => 'item'];
        $entity = $this->getDoctrine()->getManager()->getRepository(Item::class)->find($id);

        try {
            $data = json_decode($request->getContent());

            if(isset($data->title) && !is_string($data->title)){
                return $this->json([
                    'status' => 400,
                    'message' => 'Le titre doit obligatoirement etre une chaine de caractere.'
                ]);
            }
            if(isset($data->status) && !is_bool($data->status)){
                return $this->json([
                    'status' => 400,
                    'message' => 'Le statut doit etre un boolean'
                ]);
            }
                $entity->setTitle($data->title);
                $entity->setStatus($data->status);
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($data->userId);
                $todolist = $this->getDoctrine()->getManager()->getRepository(Todolist::class)->find($data->todolistId);
            if (isset($user) && isset($todolist)) {
                $entity->setUserItem($user);
                $entity->setTodolist($todolist);
            }
            else{
                return $this->json([
                    'status' => 400,
                    'message' => 'Utilisateur ou Todolist introuvable'
                ]);
            }
            return BaseController::updateEntity($id, $entity, $groups);
        }catch(NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        } 
    }

    /**
     * @Route("/item/{id}", name="item_patch_update", methods={"PATCH"})
     */
    public function patchItem($id, Request $request): JsonResponse
    {
        $groups = ['groups' => 'item'];
        $entity = $this->getDoctrine()->getManager()->getRepository(Item::class)->find($id);

        try {
            $data = json_decode($request->getContent());

            if(isset($data->title) && !is_string($data->title)){
                return $this->json([
                    'status' => 400,
                    'message' => 'Le titre doit obligatoirement etre une chaine de caractere.'
                ]);
            }
            if(isset($data->status) && !is_bool($data->status)){
                return $this->json([
                    'status' => 400,
                    'message' => 'Le statut doit etre un boolean'
                ]);
            }
            if(isset($data->title)){
                $entity->setTitle($data->title);
            }
            if(isset($data->status)){
                $entity->setStatus($data->status);
            }
            if(isset($data->userId)){
                $user = $this->getDoctrine()->getManager()->getRepository(User::class)->find($data->userId);
            }
            if(isset($data->todolistId)){
                $todolist = $this->getDoctrine()->getManager()->getRepository(Todolist::class)->find($data->todolistId);
            }
            if(isset($user) && isset($todolist)){
                $entity->setUserItem($user);
                $entity->setTodolist($todolist);
            }elseif(isset($user) && !isset($todolist)){
                $entity->setUserItem($user);
            }elseif(!isset($user) && isset($todolist)){
                $entity->setTodolist($todolist);
            }
            else{
                return $this->json([
                    'status' => 400,
                    'message' => 'Utilisateur ou Todolist introuvable'
                ]);
            }
            return BaseController::updateEntity($id, $entity, $groups);
        }catch(NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        } 
    }

    /**
     * @Route("/item/{id}", name="item_delete", methods={"DELETE"})
     */
    public function deleteItem($id): JsonResponse
    {
        return BaseController::deleteEntity($id);
    }
}
