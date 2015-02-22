<?php

use Doctrine\ORM\EntityRepository;

/**
 * EpisodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EpisodeRepository extends EntityRepository
{
    public function getNotDowloadedEpisodes()
    {
        $episodes = $this
			->createQueryBuilder('e')
				->where('e.downloaded = 0')
				->andWhere('e.firstAired <= :now')
					->setParameter('now', new \DateTime)
				->addOrderBy('e.tvshow', 'ASC')
				->addOrderBy('e.season', 'ASC')
				->addOrderBy('e.number', 'ASC')
			->getQuery()
		    ->getResult()
		;
        $response = array();
        foreach ($episodes as $episode) {
        	$id = $episode->getTvshow()->getId();
        	if (!isset($response[$id])){
        		$response[$id] = array('tvshow' => $episode->getTvshow(), 'episodes' => array());
        	}
        	array_push($response[$id]['episodes'], $episode);
        }

        return $response;
    }
}
