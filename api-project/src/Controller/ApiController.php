<?php

namespace App\Controller;

use App\Repository\CharacterRepository;
use App\Repository\NemesisRepository;
use App\Repository\SecretRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private $characterRepository;
    private $nemesisRepository;
    private $secretRepository;

    public function __construct(
        CharacterRepository $characterRepository,
        NemesisRepository $nemesisRepository,
        SecretRepository $secretRepository
    ) {
        $this->characterRepository = $characterRepository;
        $this->nemesisRepository = $nemesisRepository;
        $this->secretRepository = $secretRepository;
    }

    #[Route('/api', name: 'api', methods: ['GET'])]
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
            $ageCharacter = $character->getBorn() ? $character->getBorn()->diff(new \DateTime())->y : 0;
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
                    'gender' => $gender, // use standardized gender value
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

        $averageAgeCharacters = round($totalAgeCharacters / ($charactersCount ?: 1));
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

    #[Route('/api/characters/{id}', name: 'api_character', methods: ['GET'])]
    public function getCharacter(int $id): JsonResponse
    {
        $character = $this->characterRepository->find($id);
        if (!$character) {
            return new JsonResponse(['error' => 'Character not found'], 404);
        }

        $characterData = [
            'data' => [
                'id' => $character->getId(),
                'name' => $character->getName(),
                'gender' => $character->getGender(),
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
                    'records' => array_map(function ($nemesis) {
                        return [
                            'data' => [
                                'id' => $nemesis->getId(),
                                'character_id' => $nemesis->getCharacter()->getId(),
                                'is_alive' => $nemesis->isIsAlive(),
                                'years' => $nemesis->getYears(),
                            ],
                            'children' => [
                                'has_secret' => [
                                    'records' => array_map(function ($secret) {
                                        return [
                                            'data' => [
                                                'id' => $secret->getId(),
                                                'nemesis_id' => $secret->getNemesis()->getId(),
                                                'secret_code' => $secret->getSecretCode(),
                                            ],
                                        ];
                                    }, $nemesis->getSecrets()->toArray()),
                                ],
                            ],
                        ];
                    }, $character->getNemeses()->toArray()),
                ],
            ],
        ];

        return new JsonResponse($characterData);
    }

    #[Route('/api/nemeses/{id}', name: 'api_nemesis', methods: ['GET'])]
    public function getNemesis(int $id): JsonResponse
    {
        $nemesis = $this->nemesisRepository->find($id);
        if (!$nemesis) {
            return new JsonResponse(['error' => 'Nemesis not found'], 404);
        }

        $nemesisData = [
            'data' => [
                'id' => $nemesis->getId(),
                'character_id' => $nemesis->getCharacter()->getId(),
                'is_alive' => $nemesis->isIsAlive(),
                'years' => $nemesis->getYears(),
            ],
            'children' => [
                'has_secret' => [
                    'records' => array_map(function ($secret) {
                        return [
                            'data' => [
                                'id' => $secret->getId(),
                                'nemesis_id' => $secret->getNemesis()->getId(),
                                'secret_code' => $secret->getSecretCode(),
                            ],
                        ];
                    }, $nemesis->getSecrets()->toArray()),
                ],
            ],
        ];

        return new JsonResponse($nemesisData);
    }

    #[Route('/api/secrets/{id}', name: 'api_secret', methods: ['GET'])]
    public function getSecret(int $id): JsonResponse
    {
        $secret = $this->secretRepository->find($id);
        if (!$secret) {
            return new JsonResponse(['error' => 'Secret not found'], 404);
        }

        $secretData = [
            'data' => [
                'id' => $secret->getId(),
                'nemesis_id' => $secret->getNemesis()->getId(),
                'secret_code' => $secret->getSecretCode(),
            ],
        ];

        return new JsonResponse($secretData);
    }
}
