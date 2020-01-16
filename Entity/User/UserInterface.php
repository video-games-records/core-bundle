<?php

namespace VideoGamesRecords\CoreBundle\Entity\User;

interface UserInterface
{
    /** @return integer */
    public function getId();
    /** @return string */
    public function getUsername();
}
