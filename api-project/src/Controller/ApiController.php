<?php

namespace App\Controller;

use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/data', name: 'app_data', methods: ['GET'])]
    public function getData(CharacterRepository $characterRepository): JsonResponse
    {
        //fetching data
        $characters = $characterRepository->findAll();
        return new JsonResponse($characters);
    }
}
