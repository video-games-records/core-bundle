<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Manager\Strategy\Badge;

use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;

abstract class AbstractBadgeStrategy implements BadgeInterface
{
    private TranslatorInterface $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return TranslatorInterface
     */
    protected function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }
}
