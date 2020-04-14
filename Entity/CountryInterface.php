<?php

namespace VideoGamesRecords\CoreBundle\Entity;

interface CountryInterface
{
    /** @return integer */
    public function getId();
    /** @return string */
    public function getCodeIso2();
    /** @return string */
    public function getCodeIso3();
    /** @return string */
    public function getName();
    /** @return string */
    public function getDefaultName();
}
