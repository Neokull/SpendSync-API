<?php

namespace App\Controller\Api;

use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PersonController extends AbstractController
{
    #[Route('/api/persons', name: 'api_person_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $person = new Person();
        $person->setName($data['name'] ?? null);

        $errors = $validator->validate($person);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400); // 400 Bad Request
        }

        $em->persist($person);
        $em->flush();

        return $this->json([
            'message' => 'Person created successfully!',
            'id' => $person->getId()
        ], 201); // 201 Created
    }
}
