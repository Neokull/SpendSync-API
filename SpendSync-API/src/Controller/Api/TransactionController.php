<?php

namespace App\Controller\Api;

use App\Entity\Transaction;
use App\Repository\CategoryRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/transactions', name: 'app_transactions')]
final class TransactionController extends AbstractController
{

    #[Route('', name: 'list', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $allTransactions = $em->getRepository(Transaction::class)->findAll();
        return $this->json($allTransactions, 200, [], ['groups' => 'transaction:read']);
    }

    #[Route('/{id}', name: 'api_transaction_show', methods: ['GET'])]
    public function show(string $id, EntityManagerInterface $em): JsonResponse
    {
        if(!Uuid::isValid($id)) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        $transaction = $em->getRepository(Transaction::class)->find($id);
        if(!$transaction) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }
        return $this->json($transaction, 200, [], ['groups' => 'transaction:read']);
    }

    #[Route('/new', name: 'api_transaction_create', methods: ['POST'])]
    public function create(Request $request, CategoryRepository $categoryRepository, PersonRepository $personRepository,EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if(!is_array($data)) {
            return new JsonResponse(['error' => 'Invalid JSON body'], 400);
        }
        $date = null;
        if(isset($data['date'])) {
            try{
                $date = new \DateTime($data['date']);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Invalid date format. Use YYYY-MM-DD'], 400);
            }
        }

        $category = $categoryRepository->find($data['category'] ?? null);
        $person = $personRepository->find($data['person'] ?? null);

        $transaction = new Transaction();
        $transaction->setAmount($data['amount'] ?? null);
        $transaction->setDate($date);
        $transaction->setDescription($data['description'] ?? null);
        $transaction->setCategory($category);
        $transaction->setPerson($person);

        $errors = $validator->validate($transaction);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath(). ': ' .$error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $em->persist($transaction);
        $em->flush();

        return $this->json([
            'message' => 'Transaction created',
            'id' => $transaction->getId()
        ], 201);
    }


    #[Route('/{id}', name: 'api_transactions_update', methods: ['PUT'])]
    public function update(string $id, Request $request, EntityManagerInterface $em, CategoryRepository $categoryRepository, PersonRepository $personRepository, ValidatorInterface $validator): JsonResponse
    {
        if(!Uuid::isValid($id)) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        $transaction = $em->getRepository(Transaction::class)->find($id);
        if(!$transaction) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if(isset($data['amount'])) {
            $transaction->setAmount($data['amount']);
        }
        if(isset($data['description'])) {
            $transaction->setDescription($data['description']);
        }
        if(isset($data['date'])) {
            $transaction->setDate(new \DateTime($data['date']));
        }
        if(isset($data['category'])) {
            $category = $categoryRepository->find($data['category']);
            $transaction->setCategory($category);
        }
        if(isset($data['person'])) {
            $person = $personRepository->find($data['person']);
            $transaction->setPerson($person);
        }

        $errors = $validator->validate($transaction);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath(). ': ' .$error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], 400);
        }

        $em->flush();

        return new JsonResponse(['message' => 'Transaction updated successfully', 'id' => $transaction->getId()], 200);
    }

    #[Route('/{id}', name: 'api_transactions_delete', methods: ['DELETE'])]
    public function delete(string $id, EntityManagerInterface $em): JsonResponse
    {
        if(!Uuid::isValid($id)) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        $transaction = $em->getRepository(Transaction::class)->find($id);
        if(!$transaction) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }
        $em->remove($transaction);
        $em->flush();
        return new JsonResponse(['message' => 'Transaction deleted'], 200);
    }

}
