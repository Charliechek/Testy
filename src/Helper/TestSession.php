<?php

namespace App\Helper;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TestSession
{
    public const OTAZKY = "otazky";
    public const OTAZKY_ODPOVED = "odpoved";
    public const OTAZKY_ID = "id";
    public const AKTUALNI_OTAZKA = "aktualni";

    private SessionInterface $session;
    private array $otazky;
    private ?int $aktualni;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
        $this->otazky = $this->session->get(self::OTAZKY) ?? [];
        $this->aktualni = $this->session->get(self::AKTUALNI_OTAZKA) ?? null;
    }

    public function vratIdAktualniOtazky(): ?int
    {
        if (!isset($this->otazky[$this->aktualni])) {
            return null;
        }
        return $this->otazky[$this->aktualni][self::OTAZKY_ID];
    }

    public function posunAktualniOtazkuNaDalsi(): void
    {
        $this->session->set(self::AKTUALNI_OTAZKA, $this->aktualni + 1);
    }

    public function jeAktualniOtazkaZodpovezena(): bool
    {
        return ($this->otazky[$this->aktualni][self::OTAZKY_ODPOVED] ?? null) !== null;
    }

    public function zapisOdpoved(int $odpoved): void
    {
        $this->otazky[$this->aktualni][self::OTAZKY_ODPOVED] = $odpoved;
        $this->session->set(self::OTAZKY, $this->otazky);
    }

    public function nastavOtazky(array $otazky): void
    {
        $this->session->set(self::OTAZKY, $otazky);
    }

    public function vratOtazky(): array
    {
        return $this->otazky;
    }

    public function nastavAktualniOtazkuNaZacatek(): void
    {
        $this->session->set(self::AKTUALNI_OTAZKA, 0);
    }
}