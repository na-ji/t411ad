<?php

use Doctrine\ORM\Mapping as ORM;

/**
 * TVShow
 */
class TVShow
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
     * @var string
     */
    private $banner;

    /**
     * @var string
     */
    private $imdbId;

    /**
     * @var integer
     */
    private $tvdbId;

    /**
     * @var string
     */
    private $zap2ItId;

    /**
     * @var string
     */
    private $downloadPath;


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
     * @return TVShow
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
     * Set banner
     *
     * @param string $banner
     * @return TVShow
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return string 
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set imdbId
     *
     * @param string $imdbId
     * @return TVShow
     */
    public function setImdbId($imdbId)
    {
        $this->imdbId = $imdbId;

        return $this;
    }

    /**
     * Get imdbId
     *
     * @return string 
     */
    public function getImdbId()
    {
        return $this->imdbId;
    }

    /**
     * Set tvdbId
     *
     * @param integer $tvdbId
     * @return TVShow
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
     * Set zap2ItId
     *
     * @param string $zap2ItId
     * @return TVShow
     */
    public function setZap2ItId($zap2ItId)
    {
        $this->zap2ItId = $zap2ItId;

        return $this;
    }

    /**
     * Get zap2ItId
     *
     * @return string 
     */
    public function getZap2ItId()
    {
        return $this->zap2ItId;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $episodes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->episodes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add episodes
     *
     * @param \Episode $episodes
     * @return TVShow
     */
    public function addEpisode(\Episode $episodes)
    {
        $this->episodes[] = $episodes;

        return $this;
    }

    /**
     * Remove episodes
     *
     * @param \Episode $episodes
     */
    public function removeEpisode(\Episode $episodes)
    {
        $this->episodes->removeElement($episodes);
    }

    /**
     * Get episodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * Set downloadPath
     *
     * @param string $downloadPath
     * @return TVShow
     */
    public function setDownloadPath($downloadPath)
    {
        $this->downloadPath = $downloadPath;

        return $this;
    }

    /**
     * Get downloadPath
     *
     * @return string 
     */
    public function getDownloadPath()
    {
        return $this->downloadPath;
    }
}
