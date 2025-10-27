<?php

declare(strict_types=1);

namespace App\Infrastructure\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class JsonRequestHandler
{
    /**
     * Decode JSON request
     *
     * @return array<mixed, mixed>
     */
    public function decode(Request $request): array
    {
        if ('json' !== $request->getContentTypeFormat()) {
            throw new BadRequestHttpException('Content-Type must be application/json.');
        }

        $content = $request->getContent();
        if ('' === $content) {
            throw new BadRequestHttpException('Empty request body.');
        }

        try {
            $data = \Safe\json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('Invalid JSON: '.$e->getMessage());
        }

        if (!\is_array($data)) {
            throw new BadRequestHttpException('JSON must represent an object.');
        }

        return $data;
    }
}
