<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class IsMacArgumentValueResolver implements ArgumentValueResolverInterface {

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $argument->getName() === 'isMac' && $request->attributes->has('_isMac');
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
//        if ($request->query->has('mac')) {
//            yield $request->query->getBoolean('mac');
//            return;
//        }
//
//        $userAgent = $request->headers->get('User-Agent');
//        yield str_contains($userAgent, 'Mac');

        yield $request->attributes->get('_isMac');
    }
}