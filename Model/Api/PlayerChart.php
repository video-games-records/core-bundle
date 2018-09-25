<?php

namespace VideoGamesRecords\CoreBundle\Model\Api;

Class PlayerChart
{
    private $idPlayer;
    private $idChart;
    private $idPlatform = null;
    private $values = array();


    /**
     * Set idPlayer
     *
     * @param integer $idPlayer
     * @return $this
     */
    public function setIdPlayer($idPlayer)
    {
        $this->idPlayer = $idPlayer;
        return $this;
    }

    /**
     * Get idPlayer
     *
     * @return integer
     */
    public function getIdPlayer()
    {
        return $this->idPlayer;
    }


    /**
     * Set idChart
     *
     * @param integer $idChart
     * @return $this
     */
    public function setIdChart($idChart)
    {
        $this->idChart = $idChart;
        return $this;
    }

    /**
     * Get idChart
     *
     * @return integer
     */
    public function getIdChart()
    {
        return $this->idChart;
    }


    /**
     * Set idPlatform
     *
     * @param integer $idPlatform
     * @return $this
     */
    public function setIdPlatform($idPlatform)
    {
        $this->idPlatform = $idPlatform;
        return $this;
    }

    /**
     * Get idPlatform
     *
     * @return integer
     */
    public function getIdPlatform()
    {
        return $this->idPlatform;
    }


    /**
     * Set values
     *
     * @param array $values
     * @return $this
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }
}
