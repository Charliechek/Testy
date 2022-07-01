<?php

namespace App\Controller;

use App\Entity\Odpoved;
use App\Entity\Otazka;
use App\Form\OtazkaType;
use App\Repository\OtazkaRepository;
use App\Repository\TestRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OtazkyController extends AbstractController
{
    private OtazkaRepository $otazky;
    private TestRepository $testy;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->otazky = new OtazkaRepository($managerRegistry);
        $this->testy = new TestRepository($managerRegistry);
    }

    #[Route('/testy/{idTestu}/otazky/nova', name: 'otazky_nova')]
    public function nova(Request $request, int $idTestu): Response
    {
        $otazka = $this->vytvorPrazdnouOtazku();
        $form = $this->createForm(OtazkaType::class, $otazka);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $otazka = $form->getData();
            $test = $this->testy->find($idTestu);
            if (is_null($test)) {
                return $this->redirectToRoute("testy_neexistuje");
            }
            $otazka->setTest($test);
            $this->otazky->add($otazka, true);
            return $this->redirectToRoute("testy_upravit", ["id" => $idTestu]);
        }

        return $this->render('otazky/nova.html.twig', [
            "form" => $form->createView(),
            "idTestu" => $idTestu
        ]);
    }

    #[Route('/testy/{idTestu}/otazky/{idOtazky}/upravit', name: 'otazky_upravit')]
    public function upravit(Request $request, int $idTestu, int $idOtazky): Response
    {
        $otazka = $this->otazky->find($idOtazky);
        if (is_null($otazka) || $otazka->getTest()->getId() !== $idTestu) {
            return $this->redirectToRoute("otazky_neexistuje");
        }

        $form = $this->createForm(OtazkaType::class, $otazka);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $otazka = $form->getData();
            $this->otazky->add($otazka, true);
            return $this->redirectToRoute("testy_upravit", ["id" => $idTestu]);
        }

        return $this->render('otazky/upravit.html.twig', [
            "form" => $form->createView(),
            "idTestu" => $idTestu
        ]);
    }

    #[Route('/testy/{idTestu}/otazky/{idOtazky}/odstranit', name: 'otazky_odstranit_get', methods: ["GET"])]
    public function odstranit_get(int $idOtazky): Response
    {
        $otazka = $this->otazky->find($idOtazky);

        if (is_null($otazka)) {
            return $this->redirectToRoute("otazky_neexistuje");
        }

        return $this->render("otazky/odstranit.html.twig");
    }

    #[Route('/testy/{idTestu}/otazky/{idOtazky}/odstranit', name: 'otazky_odstranit_post', methods: ["POST"])]
    public function odstranit_post(Request $request, int $idTestu, int $idOtazky): Response
    {
        $csrf_token = $request->request->get("_token");
        if (!$this->isCsrfTokenValid('dfgsdsdffd', $csrf_token)) {
            throw new Exception("Nevalidní CSRF token.");
        }

        $otazka = $this->otazky->find($idOtazky);

        if (is_null($otazka)) {
            return $this->redirectToRoute("otazky_neexistuje");
        }

        if ($request->request->has("ano")) {
            $this->otazky->remove($otazka, true);
        }

        return $this->redirectToRoute("testy_upravit", ["id" => $idTestu]);
    }

    #[Route('/otazky/neexistuje', name: 'otazky_neexistuje', methods: ["GET"])]
    public function neexistuje(): Response
    {
        return $this->render("otazky/neexistuje.html.twig");
    }
    
    private function vytvorPrazdnouOtazku(): Otazka
    {
        $otazka = new Otazka();    
        for ($i = 1; $i <= 3; $i++) {
            $odpoved = new Odpoved();
            $otazka->addOdpovedi($odpoved);
        }
        return $otazka;
    }
}
