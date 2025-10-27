<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\DTO\FizzBuzzRequestDTO;
use App\Application\Exception\ValidationException;
use App\Application\Service\FizzBuzzService;
use App\Infrastructure\Http\JsonRequestHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class FizzBuzzController extends AbstractController
{
    public function __construct(
        private readonly FizzBuzzService $fizzBuzzService,
        private readonly ValidatorInterface $validator,
        private readonly JsonRequestHandler $jsonRequestHandler
    ) {
    }

    #[Route('/api/fizzbuzz', name: 'fizzbuzz', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $this->jsonRequestHandler->decode($request);

        // Create and validate DTO
        $dto = FizzBuzzRequestDTO::fromArray($data);
        $violations = $this->validator->validate($dto);

        if (\count($violations) > 0) {
            throw new ValidationException($violations);
        }

        // Process request
        $result = $this->fizzBuzzService->process(
            $dto->int1,
            $dto->int2,
            $dto->limit,
            $dto->str1,
            $dto->str2
        );

        return $this->json([
            'result' => $result->getItems(),
            'count' => $result->getCount(),
        ], Response::HTTP_OK);
    }
}
