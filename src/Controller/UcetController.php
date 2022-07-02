<?php

namespace App\Controller;

use App\Repository\HistorieTestuRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UcetController extends AbstractController
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route(path: "/ucet", name: "ucet_index")]
    public function index(): Response
    {
        if (in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->vytvorStrankuAdmina();
        } else {
            return $this->vytvorStrankuUzivatele();
        }
    }
    
    private function vytvorStrankuUzivatele(): Response
    {
        $historieTestu = $this->getUser()->getHistorieTestu();
    
        return $this->render("ucet/uzivatel.html.twig", [
            "historieTestu" => $historieTestu
        ]);
    }

    private function vytvorStrankuAdmina(): Response
    {
        $historieTestuUzivatele = $this->getUser()->getHistorieTestu();

        $historieTestuRepository = new HistorieTestuRepository($this->managerRegistry);
        $historieTestuOstatnich = $historieTestuRepository->matching(
            (new Criteria())->where(
                Criteria::expr()->neq("uzivatel", $this->getUser())
            )
        );

        $historieTestu = array_merge($historieTestuUzivatele->toArray(), $historieTestuOstatnich->toArray());

        return $this->render("ucet/admin.html.twig", [
            "historieTestu" => $historieTestu
        ]);
    }
}