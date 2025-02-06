<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CategoryController extends AbstractController
{
    #[Route('/api/v1/category', name: 'category', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository, SerializerInterface $serializer): JsonResponse
    {
        $categoryList = $categoryRepository->findAll();
        return $this->json($categoryList, Response::HTTP_OK, [], ['groups' => 'getCategory']);
    }

    #[Route('/api/v1/category/{id}', name: 'category_show', methods: ['GET'])]
    public function showCategory(CategoryRepository $categoryRepository, SerializerInterface $serializer, $id): JsonResponse
    {
        $category = $categoryRepository->find($id);
        if ($category) {
            return $this->json($category, Response::HTTP_OK, [], ['groups' => 'getCategory']);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/v1/category', name: 'category_create', methods: ['POST'])]
    public function createCategory(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');
        $entityManager->persist($category);
        $entityManager->flush();

        $location = $urlGenerator->generate(
            'category_show',
            ['id' => $category->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json($category, Response::HTTP_CREATED, ['Location' => $location], ['groups' => 'getCategory']);
    }

    #[Route('/api/v1/category/{id}', name: 'category_update', methods: ['PUT'])]
    public function updateCategory(Request $request, SerializerInterface $serializer, Category $currentCategory, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $updatedCategory = $serializer->deserialize($request->getContent(),
            Category::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCategory]);

        $entityManager->persist($updatedCategory);
        $entityManager->flush();

        $location = $urlGenerator->generate(
            'category_show',
            ['id' => $updatedCategory->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return $this->json(['status' => 'success'], Response::HTTP_OK, ['Location' => $location]);
    }

    #[Route('/api/v1/category/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->json(['status' => 'success'], Response::HTTP_OK);
    }
}