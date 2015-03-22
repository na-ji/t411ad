<?php

require_once __DIR__.'/../bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

use Naji\T411\Client as T411Client;

$client = new T411Client(T411_LOGIN, T411_PASSWORD, TVDB_CACHE);
$starTime = time();
setlocale(LC_TIME, 'fr_FR.UTF8');

echo "Démarrage du script le ".(strftime("%A %e %B %G à %T"))."\n";

$tvshows = $em->getRepository('Episode')->getNotDowloadedEpisodes();

foreach ($tvshows as $tvshow) {
	echo "Telechargement des episodes de ".$tvshow['tvshow']->getName()."\n";
	echo "   ".count($tvshow['episodes'])." episode".(count($tvshow['episodes']) > 1 ? 's' : '')." à télécharger\n";
	foreach ($tvshow['episodes'] as $episode) {
		$acronyme = 'S'.str_pad($episode->getSeason(), 2, "0", STR_PAD_LEFT).'E'.str_pad($episode->getNumber(), 2, "0", STR_PAD_LEFT);
		
		echo "   Saison ".$episode->getSeason()." épisode ".$episode->getNumber()." ".$acronyme."\n";
		$liens = $client->search($tvshow['tvshow']->getName().' '.$acronyme);
		$s     = count($liens) > 1 ? 's' : '';
		
		echo "      ".count($liens)." lien".$s." trouvé".$s."\n";
		if (count($liens) > 0) {
			$hd      = array();
			$full_hd = array();
			foreach ($liens as $lien) {
				if (false !== strpos($lien['name'], $acronyme))
				{
					if (strpos($lien['name'], '720p') > 0)
					{
						$hd[] = $lien;
					} else if (strpos($lien['name'], '1080p') > 0)
					{
						$full_hd[] = $lien;
					}
				}
				echo "         ".str_pad($lien['name'], 80)." ".$lien['seeders']."\n";
			}
			echo "      Recherche du meilleur lien\n";
			if (count($hd) > 0)
			{
				$max = 0;
				$bestLien;
				foreach ($hd as $lien) {
					if ($lien['seeders'] > $max) {
						$max      = $lien['seeders'];
						$bestLien = $lien;
					}
				}
				echo "         ".str_pad($bestLien['name'], 80)." ".$bestLien['seeders']."\n";
			} elseif (count($full_hd) > 0)
			{
				$max = 0;
				$bestLien;
				foreach ($full_hd as $lien) {
					if ($lien['seeders'] > $max) {
						$max      = $lien['seeders'];
						$bestLien = $lien;
					}
				}
				echo "         ".str_pad($bestLien['name'], 80)." ".$bestLien['seeders']."\n";
			} else {
				echo "      Aucun lien en qualité suffisante\n";
				break;
			}

			echo "      Enregistrement du torrent dans ".$tvshow['tvshow']->getDownloadPath()."\n";
			$client->downloadTorrent($bestLien['id'], $tvshow['tvshow']->getDownloadPath());
			$episode->setDownloaded(true);
			$em->persist($episode);
		} else {
			echo "      Aucun lien trouvé pour cet épisode...\n";
			//break;
		}
	}
}

$em->flush();
echo "Fin du script. Temps total mis : ".(time()-$starTime)."s\n\n";
