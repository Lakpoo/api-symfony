<?php

namespace App\Controller;

use App\Entity\VideoGame;
use App\Repository\VideoGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class VideoGameController extends AbstractController
{
    #[Route('/api/v1/game', name: 'game', methods: ['GET'])]
    public function index(VideoGameRepository $gameRepository, SerializerInterface $serializer): JsonResponse
    {
        $gameList = $gameRepository->findAll();

        $jsonGame = $serializer->serialize($gameList, 'json', ['groups' => 'getGame']);

        return $this->json([$jsonGame, Response::HTTP_OK, [], true, ['groups' => 'getGame']]);
    }

    #[Route('/api/v1/game/{id}', name: 'game_show', methods: ['GET'])]
    public function showGame(VideoGameRepository $gameRepository, SerializerInterface $serializer, $id): JsonResponse
    {
        $game = $gameRepository->find($id);
        if ($game) {
            return $this->json([$game, Response::HTTP_OK, [], true, ['groups' => 'getGame']]);
        }
        return new jsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/v1/game', name: 'game_create', methods: ['POST'])]
    public function createGame(Request $request, $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $videoGame = $serializer->deserialize($request->getContent(), VideoGame::class, 'json');
        $entityManager->persist($videoGame);
        $entityManager->flush();

        $location = $urlGenerator->generate(
            'game_show',
            ['id' => $videoGame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($videoGame , Response::HTTP_CREATED, ['Location' => $location], ['groups' => 'getGame']);
    }

    #[Route('/api/v1/game/{id}', name: 'game_create', methods: ['PUT'])]
    public function updateGame(Request $request, SerializerInterface $serializer, Projet $currentProjet, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedGame = $serializer->deserialize($request->getContent(),
            VideoGame::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentProjet]);

        $entityManager->persist($updatedGame);
        $entityManager->flush();

        $location = $urlGenerator->generate(
            'game_show',
            ['id' => $updatedGame->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ['Location' => $location]);
    }

    #[Route('/api/v1/game/{id}', name: 'game_create', methods: ['DELETE'])]
    public function delete(VideoGame $videoGame, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($videoGame);
        $entityManager->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }


}
