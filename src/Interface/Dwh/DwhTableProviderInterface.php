<?php
namespace VideoGamesRecords\CoreBundle\Interface\Dwh;

use DateTime;

interface DwhTableProviderInterface
{
    public function getDataForDwh(): array;

    public function getNbPostDay(DateTime $date1, DateTime $date2): array;
}