<?php

require_once __DIR__.'/../bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

use Naji\T411\Client as T411Client;

$colors = new Colors;

function echoColor($text, $foreground_color = null, $background_color = null)
{
	global $colors;
	echo $colors->getColoredString($text, $foreground_color, $background_color);
}

$client = new T411Client(T411_LOGIN, T411_PASSWORD, TVDB_CACHE, 'http://www.t411.io');
$starTime = time();
setlocale(LC_TIME, 'fr_FR.UTF8');

echo "Démarrage du script le ".(strftime("%A %e %B %G à %T"))."\n";

$tvshows = $em->getRepository('Episode')->getNotDowloadedEpisodes();

foreach ($tvshows as $tvshow) {
	echo "Telechargement des episodes de ".$tvshow['tvshow']->getName()."\n";
	echo "   ".count($tvshow['episodes'])." episode".(count($tvshow['episodes']) > 1 ? 's' : '')." à télécharger\n";
	foreach ($tvshow['episodes'] as $episode) {
		if ($episode->getNumber() == 16)
		{
			$numero = 954;
		} elseif ($episode->getNumber() == 17)
		{
			$numero = 953;
		} else {
			$numero   = ($episode->getNumber() >= 31 ? 1057 : ($episode->getNumber() >= 9 ? 937 : 936)) + $episode->getNumber();
		}
		$saison   = 967 + $episode->getSeason();
		$acronyme = 'S'.str_pad($episode->getSeason(), 2, "0", STR_PAD_LEFT).'E'.str_pad($episode->getNumber(), 2, "0", STR_PAD_LEFT);
		
		echo "   Saison ".$episode->getSeason()." épisode ".$episode->getNumber()." ".$acronyme."\n";
		$liens = $client->search(array('search' => $tvshow['tvshow']->getName().' '.$acronyme/*, 'term[46][]' => $numero, 'term[45][]' => $saison*/));
		$s     = count($liens) > 1 ? 's' : '';
		
		echo "      ".count($liens)." lien".$s." trouvé".$s."\n";
		if (count($liens) > 0) {
			$hd      = array();
			$full_hd = array();
			foreach ($liens as $lien) {
				$color = 'red';
				if (false === strpos($lien['name'], $acronyme))
				{
					$color = 'red';
				} else {
					if (strpos($lien['name'], '720p') > 0)
					{
						$hd[] = $lien;
						$color = 'green';
					} else if (strpos($lien['name'], '1080p') > 0)
					{
						$full_hd[] = $lien;
						$color = 'green';
					}
				}
				echoColor("         ".str_pad($lien['name'], 80)." ".$lien['seeder']."\n", $color);
			}
			echo "      Recherche du meilleur lien\n";
			if (count($hd) > 0)
			{
				$max = 0;
				$bestLien;
				foreach ($hd as $lien) {
					if ($lien['seeder'] > $max) {
						$max      = $lien['seeder'];
						$bestLien = $lien;
					}
				}
				echoColor("         ".str_pad($bestLien['name'], 80)." ".$bestLien['seeder']."\n", $color);
			} elseif (count($full_hd) > 0)
			{
				$max = 0;
				$bestLien;
				foreach ($full_hd as $lien) {
					if ($lien['seeder'] > $max) {
						$max      = $lien['seeder'];
						$bestLien = $lien;
					}
				}
				echoColor("         ".str_pad($bestLien['name'], 80)." ".$bestLien['seeder']."\n", "green");
			} else {
				echoColor("      Aucun lien en qualité suffisante\n", null, "red");
				break;
			}

			echo "      Enregistrement du torrent dans ".$tvshow['tvshow']->getDownloadPath()."\n";
			$client->downloadTorrent('http://'.$bestLien['url'], $tvshow['tvshow']->getDownloadPath());
			$episode->setDownloaded(true);
			$em->persist($episode);
		} else {
			echo "      Aucun lien trouvé pour cet épisode...\n";
			//break;
		}
	}
}

$em->flush();
echo "Fin du script. Temps total mis : ".(time()-$starTime)."s\n";