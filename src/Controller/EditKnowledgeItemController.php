<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\EditKnowledgeItemType;
use App\Service\KnowledgeService;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

final readonly class EditKnowledgeItemController
{
    public function __construct(
        private Environment $twig,
        private KnowledgeService $service,
        private FormFactoryInterface $formFactory,
    ) {}

    public function __invoke(Request $request): Response
    {
        $id = $request->getAttribute('id');
        $item = is_numeric($id) ? $this->service->getItem((int) $id) : null;

        $form = $this->formFactory->create(EditKnowledgeItemType::class, $item);
        $form->handleRequest();

        if ($form->isSubmitted()) {
            $this->service->addItem($form->getData()['content']);

            return new Response(302, ['Location' => '/knowledge/items']);
        }

        return new Response(body: $this->twig->render('KnowledgeBase/edit.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]));
    }
}
