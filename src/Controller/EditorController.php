<?php

namespace App\Controller;

use App\Entity\Editor;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class EditorController extends AbstractController
{
    #[Route('/api/v1/editor', name: 'editor', methods: ['GET'])]
    public function index(EditorRepository $editorRepository, SerializerInterface $serializer): JsonResponse
    {
        $editorList = $editorRepository->findAll();

        $jsonEditor = $serializer->serialize($editorList, 'json', ['groups' => 'getEditor']);

        return $this->json([$jsonEditor, JsonResponse::HTTP_OK, [], true, ['groups' => 'getEditor']]);
    }

    #[Route('/api/v1/editor/{id}', name: 'editor_show', methods: ['GET'])]
    public function showEditor(EditorRepository $editorRepository, SerializerInterface $serializer, $id): JsonResponse
    {
        $editor = $editorRepository->find($id);
        if ($editor) {
            return $this->json([$editor, JsonResponse::HTTP_OK, [], true, ['groups' => 'getEditor']]);
        }
        return new JsonResponse(null, JsonResponse::HTTP_NOT_FOUND);
    }

    #[Route('/api/v1/editor', name: 'editor_create', methods: ['POST'])]
    public function createEditor(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $editor = $serializer->deserialize($request->getContent(), Editor::class, 'json');
        $entityManager->persist($editor);
        $entityManager->flush();

        $location = $urlGenerator->generate(
            'editor_show',
            ['id' => $editor->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($editor, JsonResponse::HTTP_CREATED, ['Location' => $location], ['groups' => 'getEditor']);
    }

    #[Route('/api/v1/editor/{id}', name: 'editor_update', methods: ['PUT'])]
    public function updateEditor(Request $request, SerializerInterface $serializer, Editor $currentEditor, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedEditor = $serializer->deserialize($request->getContent(),
            Editor::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentEditor]);

        $entityManager->persist($updatedEditor);
        $entityManager->flush();

        $location = $urlGenerator->generate(
            'editor_show',
            ['id' => $updatedEditor->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], JsonResponse::HTTP_OK, ['Location' => $location]);
    }

    #[Route('/api/v1/editor/{id}', name: 'editor_delete', methods: ['DELETE'])]
    public function delete(Editor $editor, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($editor);
        $entityManager->flush();

        return $this->json(['status' => 'success'], JsonResponse::HTTP_OK);
    }
}