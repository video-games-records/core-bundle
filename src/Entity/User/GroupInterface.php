<?php

namespace VideoGamesRecords\CoreBundle\Entity\User;

interface GroupInterface
{
    /** @return integer */
    public function getId();
    /** @return string */
    public function getName();
}
