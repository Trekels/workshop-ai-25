<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\KnowledgeItemRepository;
use GuzzleHttp\Psr7\Response;
use Twig\Environment;

final readonly class KnowledgeListController
{
    public function __construct(
        private Environment $twig,
        private KnowledgeItemRepository $repository,
    ) {}

    public function __invoke(): Response
    {
        return new Response(body: $this->twig->render('KnowledgeBase/list.html.twig', [
            'items' => $this->repository->fetchAll(),
        ]));
    }
}
