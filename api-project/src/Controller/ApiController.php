<?php

// src/Controller/ApiController.php
namespace App\Controller;

use App\Repository\CharacterRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/characters', name: 'api_characters', methods: ['GET'])]
    public function getCharacters(CharacterRepository $characterRepository): JsonResponse
    {
        $characters = $characterRepository->findAll();
        $charactersCount = count($characters);
        $totalAgeCharacters = 0;
        $totalWeightCharacters = 0;
        $totalWeightCount = 0;
        $genders = ['female' => 0, 'male' => 0, 'other' => 0];
        $totalAgeNemeses = 0;
        $nemesesCount = 0;

        $charactersData = [];
        foreach ($characters as $character) {
            $ageCharacter = $character->getBorn() ? $character->getBorn()->diff(new DateTime())->y : 0;
            $totalAgeCharacters += $ageCharacter;
            if ($character->getWeight() !== null) {
                $totalWeightCharacters += $character->getWeight();
                $totalWeightCount++;
            }

            $gender = strtolower($character->getGender());
            if (in_array($gender, ['female', 'f'])) {
                $gender = 'female';
            } elseif (in_array($gender, ['male', 'm'])) {
                $gender = 'male';
            } else {
                $gender = 'other';
            }
            $genders[$gender] += 1;

            $nemesesData = [];
            foreach ($character->getNemeses() as $nemesis) {
                $ageNemesis = $nemesis->getYears();
                $totalAgeNemeses += $ageNemesis;
                $nemesesCount++;
                $secretsData = [];
                foreach ($nemesis->getSecrets() as $secret) {
                    $secretsData[] = [
                        'data' => [
                            'id' => $secret->getId(),
                            'nemesis_id' => $secret->getNemesis()->getId(),
                            'secret_code' => $secret->getSecretCode(),
                        ],
                    ];
                }

                $nemesesData[] = [
                    'data' => [
                        'id' => $nemesis->getId(),
                        'character_id' => $character->getId(),
                        'is_alive' => $nemesis->isIsAlive(),
                        'years' => $nemesis->getYears(),
                    ],
                    'children' => [
                        'has_secret' => [
                            'records' => $secretsData,
                        ],
                    ],
                ];
            }

            $charactersData[] = [
                'data' => [
                    'id' => $character->getId(),
                    'name' => $character->getName(),
                    'gender' => $gender, //use standardized gender value
                    'ability' => $character->getAbility(),
                    'minimal_distance' => $character->getMinimalDistance(),
                    'weight' => $character->getWeight(),
                    'born' => $character->getBorn() ? $character->getBorn()->format('Y-m-d H:i:s') : null,
                    'in_space_since' => $character->getInSpaceSince() ? $character->getInSpaceSince()->format('Y-m-d H:i:s') : null,
                    'beer_consumption' => $character->getBeerConsumption(),
                    'knows_the_answer' => $character->isKnowsTheAnswer(),
                ],
                'children' => [
                    'has_nemesis' => [
                        'records' => $nemesesData,
                    ],
                ],
            ];
        }

        $averageAgeCharacters = $totalAgeCharacters / ($charactersCount ?: 1);
        $averageWeightCharacters = $totalWeightCharacters / ($totalWeightCount ?: 1);
        $averageAgeNemeses = $totalAgeNemeses / ($nemesesCount ?: 1);

        $response = [
            'characters_count' => $charactersCount,
            'average_age_characters' => $averageAgeCharacters,
            'average_age_nemeses' => $averageAgeNemeses,
            'average_weight_characters' => $averageWeightCharacters,
            'genders' => $genders,
            'characters' => $charactersData,
        ];

        return new JsonResponse($response);
    }
}
