<?php

namespace App\Helper;

use App\Entity\HistorieTestu;
use App\Entity\Otazka;
use App\Entity\Test as EntityTest;
use App\Helper\TestSession;
use App\Repository\HistorieTestuRepository;
use App\Repository\OtazkaRepository;
use App\Repository\TestRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class Test
{
    private ManagerRegistry $managerRegistry;
    private OtazkaRepository $otazky;
    private ?UserInterface $uzivatel;
    private TestSession $session;

    public function __construct(ManagerRegistry $managerRegistry, RequestStack $requestStack, Security $security)
    {
        $this->managerRegistry = $managerRegistry;
        $this->otazky = new OtazkaRepository($managerRegistry);
        $this->uzivatel = $security->getUser();
        $this->session = new TestSession($requestStack->getSession());
    }

    public function zacniNovyTest(EntityTest $test): void
    {
        $otazky = $test->getOtazky()->map(
            fn($otazka) => [
                TestSession::OTAZKY_ID => $otazka->getId(), 
                TestSession::OTAZKY_ODPOVED => null
            ]
        )->toArray();
        
        $this->session->nastavOtazky($otazky);
        $this->session->nastavAktualniOtazkuNaZacatek();
        $this->session->nastavIdTestu($test->getId());
    }
        
    public function vratAktualniOtazku(): ?Otazka
    {
        $id = $this->session->vratIdAktualniOtazky();
        return $this->otazky->find($id ?? "");
    }

    public function odpovez(int $odpoved): array
    {
        $this->session->zapisOdpoved($odpoved);
        $spravnaOdpoved = $this->vratAktualniOtazku()->getSpravnaOdpoved();       
        return $this->vytvorZpravuVyhodnoceniOdpovedi($odpoved, $spravnaOdpoved);
    }

    public function vyhodnotTest(): string
    {
        $otazkySession = $this->session->vratOtazky();
        $pocetSpravnychOdpovedi = $this->spocitejSpravneOdpovedi($otazkySession);
        $pocetOtazek = count($otazkySession);
        $this->ulozTest($pocetSpravnychOdpovedi, $pocetOtazek);
        return "Opověděli jste správně na $pocetSpravnychOdpovedi z $pocetOtazek otázek.";
    }

    public function ulozTest(int $pocetSpravnychOdpovedi, int $pocetOtazek): void
    {
        $historieTestu = new HistorieTestu();
        $testy = new TestRepository($this->managerRegistry);
        $idTestu = $this->session->vratIdTestu();
        $historieTestu
            ->setPocetSpravnychOdpovedi($pocetSpravnychOdpovedi)
            ->setPocetOtazek($pocetOtazek)
            ->setTest($testy->find($idTestu))
            ->setUzivatel($this->uzivatel)
            ->setCas(new DateTime())
        ;
        $historieTestuRepository = new HistorieTestuRepository($this->managerRegistry);
        $historieTestuRepository->add($historieTestu, true);
    }

    public function jeAktualniOtazkaZodpovezena(): bool
    {
        return $this->session->jeAktualniOtazkaZodpovezena();
    }

    public function posunNaDalsiOtazku(): void
    {
        $this->session->posunAktualniOtazkuNaDalsi();
    }

    private function vytvorZpravuVyhodnoceniOdpovedi(int $odpovedUzivatele, int $odpovedSpravna): array
    {
        if ($odpovedUzivatele === $odpovedSpravna) {
            $zprava = [
                "trida" => "alert-success",
                "text" => "Správně."
            ];
        } else {
            $zprava = [
                "trida" => "alert-danger",
                "text" => "Špatně. Správná odpověď je " . range("A", "Z")[$odpovedSpravna] . "."
            ];
        }
        return $zprava;
    }

    private function spocitejSpravneOdpovedi(array $otazkySession): int
    {
        $pocetSpravnychOdpovedi = 0;
        foreach ($otazkySession as $otazkaSession) {
            $id = $otazkaSession[TestSession::OTAZKY_ID];
            $otazka = $this->otazky->find($id);
            if ($otazka->getSpravnaOdpoved() === $otazkaSession[TestSession::OTAZKY_ODPOVED]) {
                $pocetSpravnychOdpovedi++;
            }
        }
        return $pocetSpravnychOdpovedi;
    }
}