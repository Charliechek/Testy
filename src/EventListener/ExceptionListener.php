<?php

namespace App\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener extends AbstractController
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new Response();
        
        if ($exception instanceof HttpExceptionInterface) {
            $kodChyby = $exception->getStatusCode();
            $response->headers->replace($exception->getHeaders());
        } else {
            $kodChyby = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        
        if ($this->container->get('twig')->getLoader()->exists("chyba/$kodChyby.html.twig")) {
            $obsah = $this->renderView("chyba/$kodChyby.html.twig");
        } else {
            $obsah = "Chyba $kodChyby:<br>" . $exception->getMessage();
        }

        $response->setContent($obsah);
        $response->setStatusCode($kodChyby);

        $event->setResponse($response);
    }
}