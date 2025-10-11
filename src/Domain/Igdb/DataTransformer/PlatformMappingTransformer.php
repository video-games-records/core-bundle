<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Domain\Igdb\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use VideoGamesRecords\CoreBundle\Domain\Igdb\Service\PlatformMappingService;

/**
 * Transform between VGR Platform IDs and IGDB Platform IDs in forms
 */
class PlatformMappingTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly PlatformMappingService $platformMappingService
    ) {
    }

    /**
     * Transform VGR Platform ID to IGDB Platform ID (for form display)
     */
    public function transform(mixed $value): mixed
    {
        if ($value === null || !is_int($value)) {
            return null;
        }

        return $this->platformMappingService->getIgdbPlatformId($value);
    }

    /**
     * Transform IGDB Platform ID back to VGR Platform ID (for form submission)
     */
    public function reverseTransform(mixed $value): mixed
    {
        if ($value === null || !is_int($value)) {
            return null;
        }

        return $this->platformMappingService->getVgrPlatformId($value);
    }
}
