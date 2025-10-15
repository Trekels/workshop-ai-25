<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PromptType;
use App\Service\ConversationService;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;

final readonly class PromptController
{
    public function __construct(
        private Environment $twig,
        private FormFactoryInterface $formFactory,
        private ConversationService $conversationService,
    ) {}

    public function __invoke(): Response
    {
        $form = $this->formFactory->create(PromptType::class);
        $form->handleRequest();

        if ($form->isSubmitted()) {
            $this->conversationService->handleUserPrompt($form->getData()['prompt']);

            return new Response(302, ['Location' => '/']);
        }

        return new Response(body: $this->twig->render('Prompt/prompt.html.twig', [
            'form' => $form->createView(),
            'messages' => $this->conversationService->loadConversation(),
        ]));
    }
}
