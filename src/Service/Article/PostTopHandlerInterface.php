<?php
namespace VideoGamesRecords\CoreBundle\Service\Article;

interface PostTopHandlerInterface
{
    public function handle($day): void;
}