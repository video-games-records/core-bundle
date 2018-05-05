<?php

namespace VideoGamesRecords\CoreBundle\Entity;

interface UserInterface
{
    /** @return integer */
    public function getId();
    /** @return string */
    public function getUsername();
}
