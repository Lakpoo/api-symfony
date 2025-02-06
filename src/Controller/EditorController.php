<?php

namespace App\Controller;

use App\Entity\Editor;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        return $this->json($editorList, Response::HTTP_OK, [], ['groups' => 'getEditor']);
    }

    #[Route('/api/v1/editor/{id}', name: 'editor_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showEditor(EditorRepository $editorRepository, SerializerInterface $serializer, $id): JsonResponse
    {
        $editor = $editorRepository->find($id);
        if ($editor) {
            return $this->json($editor, Response::HTTP_OK, [], ['groups' => 'getEditor']);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
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

        return $this->json($editor, Response::HTTP_CREATED, ['Location' => $location], ['groups' => 'getEditor']);
    }

    #[Route('/api/v1/editor/{id}', name: 'editor_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
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

        return $this->json(['status' => 'success'], Response::HTTP_OK, ['Location' => $location]);
    }

    #[Route('/api/v1/editor/{id}', name: 'editor_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteEditor(Editor $editor, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($editor);
        $entityManager->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }
}