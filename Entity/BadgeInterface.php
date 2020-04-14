<?php

namespace VideoGamesRecords\CoreBundle\Entity;

interface BadgeInterface
{
    /** @return integer */
    public function getId();
    /** @return string */
    public function getType();
    /** @return string */
    public function getPicture();
    /** @return integer */
    public function getValue();
}
