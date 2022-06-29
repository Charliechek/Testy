<?php

namespace App\Controller;

use App\Helper\Test;
use App\Repository\TestRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private Test $test;
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry, RequestStack $requestStack)
    {
        $this->test = new Test($managerRegistry, $requestStack);
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/test/{id}/spustit', name: 'test_spustit')]
    public function test_spustit(int $id): Response
    {
        $testy = new TestRepository($this->managerRegistry);
        $test = $testy->find($id);

        if (is_null($test)) {
            return $this->redirectToRoute("testy_neexistuje");
        }

        $this->test->zacniNovyTest($test);

        return $this->redirectToRoute("test_otazka_zobrazeni");
    }

    #[Route('/test/otazka', name: 'test_otazka_zobrazeni', methods: ["GET"])]
    public function otazka_zobrazeni(): Response
    {
        if ($this->test->jeAktualniOtazkaZodpovezena()) {
            $this->test->posunNaDalsiOtazku();
            return $this->redirectToRoute("test_otazka_zobrazeni");
        }

        $otazka = $this->test->vratAktualniOtazku();
        if (is_null($otazka)) {
            return $this->redirectToRoute("test_konec");
        }

        return $this->render('test/otazka_zobrazeni.html.twig', [
            "otazka" => $otazka
        ]);
    }

    #[Route('/test/otazka', name: 'test_otazka_odpovezeni', methods: ["POST"])]
    public function otazka_odpovezeni(Request $request): Response
    {
        if ($request->request->has("dalsi_otazka")) {
            $this->test->posunNaDalsiOtazku();
            return $this->redirectToRoute("test_otazka_zobrazeni");
        }

        $otazka = $this->test->vratAktualniOtazku();
        $odpoved = $request->get("odpoved");        
        $zprava = $this->test->odpovez($odpoved);

        return $this->render('test/otazka_odpovezeni.html.twig', [
            "otazka" => $otazka,
            "odpoved" => $odpoved,
            "zprava" => $zprava
        ]);
    }

    #[Route('/test/konec', name: 'test_konec')]
    public function test_konec(): Response
    {
        $vyhodnoceni = $this->test->vyhodnotTest();

        return $this->render("test/konec.html.twig", [
            "vyhodnoceni" =>  $vyhodnoceni
        ]);
    }
}
