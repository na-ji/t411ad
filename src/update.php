<?php

require_once __DIR__.'/../bootstrap.php';

use Moinax\TvDb\Client;

header('Content-Type: text/plain; charset=utf-8');

$starTime = time();
setlocale(LC_TIME, 'fr_FR.UTF8');

echo "Démarrage du script le ".(strftime("%A %e %B %G à %T"))."\n";

$tvshows  = $em->getRepository('TVShow')->findAll();
$episodes = array();
$tvdb     = new Client(TVDB_URL, TVDB_API_KEY);

function findEpisodeByTvdbId($episodes, $tvdb_id)
{
	$total = $episodes->count();
	for ($i = $total - 1; $i >= 0; $i--) { 
		if($episodes->get($i)->getTvdbId() == $tvdb_id)
		{
			return $i;
		}
	}
	return -1;
}

foreach ($tvshows as $tvshow) {
	$tvdb_epis                  = $tvdb->getSerieEpisodes($tvshow->getTvdbId());
	$episodes[$tvshow->getId()] = $tvshow->getEpisodes();
	//var_dump($episodes);

	// First we check if there is new episodes
	echo "Vérification s'il y a de nouveaux épisodes pour ".$tvshow->getName()."\n";
	if (($diff = count($tvdb_epis['episodes']) - $episodes[$tvshow->getId()]->count()) > 0)
	{
		$s   = $diff > 1 ? 's' : '';
		$aux = $diff > 1 ? 'eaux' : 'el';
		echo "   ".$diff." nouv".$aux." épisode".$s." disponible".$s."\n";
		//var_dump($tvdb_epis['episodes']);
		$j = 0;
		foreach (array_reverse($tvdb_epis['episodes']) as $tvdb_epi) { 
			if (findEpisodeByTvdbId($episodes[$tvshow->getId()], $tvdb_epi->id) < 0)
			{
				$j++;
				$acronyme = 'S'.str_pad($tvdb_epi->season, 2, "0", STR_PAD_LEFT).'E'.str_pad($tvdb_epi->number, 2, "0", STR_PAD_LEFT);
				echo "      ".$acronyme." est nouveau\n";
				$episode = new Episode;
				$episode
					->setName($tvdb_epi->name)
					->setNumber($tvdb_epi->number)
					->setSeason($tvdb_epi->season)
					->setTvdbId($tvdb_epi->id)
					->setFirstAired($tvdb_epi->firstAired)
					->setThumbnail($tvdb_epi->thumbnail)
					->setTvshow($tvshow)
					->setDownloaded(false)
				;
				$em->persist($episode);
			}
			if ($j == $diff)
			{
				//echo "      On s'arrête là\n";
				break;
			}
		}
	} else {
		echo "   Aucune nouveauté\n";
	}

	// Then we check if we have something to update
	echo "Vérification s'il y a des mises à jour pour ".$tvshow->getName()."\n";
	$total = $episodes[$tvshow->getId()]->count();
	//var_dump($tvdb_epis);
	for ($i = $total - 1; $i >= 0; $i--) { 
		$episode  = $episodes[$tvshow->getId()]->get($i);
		if ($episode->getDownloaded())
			break;
		$tvdb_epi = $tvdb_epis['episodes'][$episode->getTvdbId()];
		$acronyme = 'S'.str_pad($tvdb_epi->season, 2, "0", STR_PAD_LEFT).'E'.str_pad($tvdb_epi->number, 2, "0", STR_PAD_LEFT);
		if($tvdb_epi->name != $episode->getName() || $tvdb_epi->firstAired != $episode->getFirstAired() || $tvdb_epi->thumbnail != $episode->getThumbnail())
		{
			echo "   ".$acronyme." est à mettre-à-jour\n";
			$episode
				->setName($tvdb_epi->name)
				->setNumber($tvdb_epi->number)
				->setSeason($tvdb_epi->season)
				->setTvdbId($tvdb_epi->id)
				->setFirstAired($tvdb_epi->firstAired)
				->setThumbnail($tvdb_epi->thumbnail)
				->setTvshow($tvshow)
				->setDownloaded(false)
			;
			$em->persist($episode);
		} else {
			echo "   ".$acronyme." est à jour\n";
		}
	}
}

$em->flush();
echo "Fin du script. Temps total mis : ".(time()-$starTime)."s\n\n";
