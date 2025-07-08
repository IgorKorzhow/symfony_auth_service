<?php

namespace App\ArgumentResolver;

use App\Dto\AbstractValidationDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DtoValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dtoClass = $argument->getType();

        if (
            !$dtoClass
            || !is_subclass_of($dtoClass, AbstractValidationDto::class)
        ) {
            return [];
        }

        $data = json_decode($request->getContent(), true) ?? [];

        yield new $dtoClass($data);
    }
}

