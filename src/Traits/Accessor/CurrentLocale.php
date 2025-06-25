<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Accessor;

trait CurrentLocale
{
    private ?string $currentLocale = null;

    public function setCurrentLocale(string $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function getCurrentLocale(): ?string
    {
        return $this->currentLocale;
    }
}
