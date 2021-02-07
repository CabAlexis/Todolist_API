<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

interface InterfaceController
{
    public function getEntity(): JsonResponse;

    public function getOneEntity($arg): JsonResponse;

    public function createEntity($arg): JsonResponse;

    public function updateEntity($arg, $entity): JsonResponse;

    public function deleteEntity($arg): JsonResponse;
}
