<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface InterfaceController
{
    public function getEntity(): Response;

    public function getOneEntity($arg): Response;

    public function createEntity(Request $request): Response;

    public function updateEntity($arg, Request $request): Response;

    public function deleteEntity($arg): Response;
}
