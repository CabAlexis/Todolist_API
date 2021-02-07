<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends BaseController
{

    public function __construct(EntityManagerInterface $em)
    {
       parent::__construct(Category::class, $em);
    }

    /**
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function getCategories(): JsonResponse
    {
        return BaseController::getEntity();
    }

    /**
     * @Route("/category/{id}", name="one_category", methods={"GET"})
     */
    public function getOneCategory($id): JsonResponse
    {
        return BaseController::getOneEntity($id);
    }

    /**
     * @Route("/category", name="category_create", methods={"POST"})
     */
    public function createCategory(Request $request, ValidatorInterface $validator, SerializerInterface $serializer): JsonResponse
    {
        try {
            $data = $request->getContent();

            $entity = $serializer->deserialize($data, Category::class, 'json');

            $errors = $validator->validate($entity);

            if(count($errors) > 0){
                return $this->json($errors, 400);
            }
            return BaseController::createEntity($entity);
        }catch(NotEncodableValueException $e){
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @Route("/category/{id}", name="category_update", methods={"PUT"})
     */
    public function updateCategory($id, Request $request): JsonResponse
    {
        $entity = $this->getDoctrine()->getManager()->getRepository(Category::class)->find($id);
        $data = json_decode($request->getContent());
        $entity->setTitle($data->title);

        return BaseController::updateEntity($id, $entity);
    }

    /**
     * @Route("/category/{id}", name="category_delete", methods={"DELETE"})
     */
    public function deleteCategory($id): JsonResponse
    {
        return BaseController::deleteEntity($id);
    }
}
