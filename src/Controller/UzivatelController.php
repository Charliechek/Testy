<?php

namespace App\Controller;

use App\Form\RegistraceUzivateleType;
use App\Repository\UzivatelRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UzivatelController extends AbstractController
{
    private UzivatelRepository $uzivatele;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->uzivatele = new UzivatelRepository($managerRegistry);
    }

    #[Route(path: "/registrace", name: "uzivatel_registrace")]
    public function registrace(Request $request): Response
    {
        $form = $this->createForm(RegistraceUzivateleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uzivatel = $form->getData();
            $uzivatel->setRoles(["ROLE_UZIVATEL"]);
            $uzivatel->zasifrujHeslo();
            $this->uzivatele->add($uzivatel, true);
            $this->addFlash("registrace", "Úspěšně jste se zaregistrovali.");
            return $this->redirectToRoute("uzivatel_registrace");
        }

        return $this->render("uzivatel/registrace.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route(path: '/prihlaseni', name: 'uzivatel_prihlaseni')]
    public function prihlaseni(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('ucet_index');
        }

        return $this->render('uzivatel/prihlaseni.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(), 
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route(path: '/odhlaseni', name: 'uzivatel_odhlaseni')]
    public function odhlaseni(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
