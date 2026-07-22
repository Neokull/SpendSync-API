<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransactionController extends AbstractController
{
    #[Route('/api/transaction', name: 'app_api_transaction')]
    public function index(): Response
    {
        return $this->render('api/transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }
}
