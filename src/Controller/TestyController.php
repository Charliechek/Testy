<?php

namespace App\Controller;

use App\Form\NovyTestType;
use App\Repository\HistorieTestuRepository;
use App\Repository\TestRepository;
use DateTime;
use Exception;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TestyController extends AbstractController
{
    private ManagerRegistry $managerRegistry;
    private TestRepository $testy;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->testy = new TestRepository($managerRegistry);
    }

    #[Route('/testy/index', name: 'testy_index')]
    public function index(): Response
    {
        return $this->render("testy/index.html.twig", [
            "testy" => $this->testy->findAll()
        ]);
    }

    #[Route('/testy/novy', name: 'testy_novy')]
    public function novy(Request $request): Response
    {
        $form = $this->createForm(NovyTestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $novyTest = $form->getData();
            $novyTest->setCasVytvoreni(new DateTime());        
            $this->testy->add($novyTest, true);
            return $this->redirectToRoute("testy_index");
        }

        return $this->render("testy/novy.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/testy/{id}/upravit', name: 'testy_upravit')]
    public function upravit(Request $request, int $id): Response
    {
        $test = $this->testy->find($id);

        if (is_null($test)) {
            return $this->redirectToRoute("testy_neexistuje");
        }

        $form = $this->createForm(NovyTestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $upravenyTest = $form->getData();  
            $this->testy->add($upravenyTest, true);
            return $this->redirectToRoute("testy_index");
        }

        return $this->render("testy/upravit.html.twig", [
            "form" => $form->createView(),
            "otazky" => $test->getOtazky(),
            "idTestu" => $id
        ]);
    }

    #[Route('/testy/{id}/odstranit', name: 'testy_odstranit_get', methods: ["GET"])]
    public function odstranit_get(int $id): Response
    {
        $test = $this->testy->find($id);

        if (is_null($test)) {
            return $this->redirectToRoute("testy_neexistuje");
        }

        return $this->render("testy/odstranit.html.twig");
    }

    #[Route('/testy/{id}/odstranit', name: 'testy_odstranit_post', methods: ["POST"])]
    public function odstranit_post(Request $request, int $id): Response
    {
        $csrf_token = $request->request->get("_token");
        if (!$this->isCsrfTokenValid('rzjjgkjhj', $csrf_token)) {
            throw new Exception("Nevalidní CSRF token.");
        }

        $test = $this->testy->find($id);

        if (is_null($test)) {
            return $this->redirectToRoute("testy_neexistuje");
        }

        if ($request->request->has("ano")) {
            $this->testy->remove($test, true);
        }

        return $this->redirectToRoute("testy_index");
    }

    #[Route('/testy/neexistuje', name: 'testy_neexistuje', methods: ["GET"])]
    public function neexistuje(): Response
    {
        return $this->render("testy/neexistuje.html.twig");
    }
}
