<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * Episode
 */
class Episode
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $number;

    /**
     * @var integer
     */
    private $season;

    /**
     * @var integer
     */
    private $tvdbId;

    /**
     * @var \DateTime
     */
    private $firstAired;

    /**
     * @var string
     */
    private $thumbnail;

    /**
     * @var \TVShow
     */
    private $tvshow;

    /**
     * @var boolean
     */
    private $downloaded;

    public function __construct()
    {
        $this->firstAired = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Episode
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return Episode
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set season
     *
     * @param integer $season
     * @return Episode
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return integer 
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Set tvdbId
     *
     * @param integer $tvdbId
     * @return Episode
     */
    public function setTvdbId($tvdbId)
    {
        $this->tvdbId = $tvdbId;

        return $this;
    }

    /**
     * Get tvdbId
     *
     * @return integer 
     */
    public function getTvdbId()
    {
        return $this->tvdbId;
    }

    /**
     * Set firstAired
     *
     * @param \DateTime $firstAired
     * @return Episode
     */
    public function setFirstAired($firstAired)
    {
        $this->firstAired = $firstAired;

        return $this;
    }

    /**
     * Get firstAired
     *
     * @return \DateTime 
     */
    public function getFirstAired()
    {
        return $this->firstAired;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Episode
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string 
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set tvshow
     *
     * @param \TVShow $tvshow
     * @return Episode
     */
    public function setTvshow(\TVShow $tvshow = null)
    {
        $this->tvshow = $tvshow;

        return $this;
    }

    /**
     * Get tvshow
     *
     * @return \TVShow 
     */
    public function getTvshow()
    {
        return $this->tvshow;
    }

    /**
     * Set downloaded
     *
     * @param boolean $downloaded
     * @return Episode
     */
    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;

        return $this;
    }

    /**
     * Get downloaded
     *
     * @return boolean 
     */
    public function getDownloaded()
    {
        return $this->downloaded;
    }
}
