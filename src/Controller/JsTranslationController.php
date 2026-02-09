<?php

namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
class JsTranslationController
{
    #[Route('/js-translations', name: 'js_translations')]
    public function __invoke(TranslatorInterface $translator): JsonResponse
    {
        // agrega todas las keys que necesites en JS
        $messages = [
            'actions' => $translator->trans('actions'),
            'edit' => $translator->trans('edit'),
            'enable' => $translator->trans('enable'),
            'disable' => $translator->trans('disable'),
            'product.category.add' => $translator->trans('product.category.add'),
            'product.category.edit' => $translator->trans('product.category.edit'),
            'product.subcategory.add' => $translator->trans('product.subcategory.add'),
            'product.subcategory.edit' => $translator->trans('product.subcategory.edit'),
        ];

        return new JsonResponse($messages);
    }
}