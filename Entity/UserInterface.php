<?php

namespace VideoGamesRecords\CoreBundle\Entity;

interface UserInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return string
     */
    public function getUsername();
}
