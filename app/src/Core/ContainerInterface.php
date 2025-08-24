<?php

declare(strict_types=1);

namespace App\Core;

interface ContainerInterface
{
    /**
     * Holt einen Eintrag aus dem Container.
     *
     * @param string $id Die Service-ID oder Klassenname
     * @return mixed
     * @throws \RuntimeException Wenn der Eintrag nicht gefunden wird.
     */
    public function get(string $id);

    /**
     * Prüft, ob ein Eintrag existiert.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;
}