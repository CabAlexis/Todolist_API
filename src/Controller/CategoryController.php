<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends BaseController
{

    public function __construct(EntityManagerInterface $em)
    {
       parent::__construct(Category::class, $em);
    }

    /**
     * @Route("/categories", name="categories", methods={"GET"})
     */
    public function getCategories(): Response
    {
        return BaseController::getEntity();
    }

    /**
     * @Route("/category", name="category_create", methods={"POST"})
     */
    public function createCategory(Request $request): Response
    {
        return BaseController::createEntity($request);
    }

    /**
     * @Route("/categories/{id}", name="category_update", methods={"PUT"})
     */
    public function updateCategory($id, Request $request): Response
    {
        return BaseController::updateEntity($id, $request);
    }
}
