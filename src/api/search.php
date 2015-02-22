<?php

require_once __DIR__.'/../../bootstrap.php';

use Moinax\TvDb\Client;
use Moinax\TvDb\Http\Cache\FilesystemCache;
use Moinax\TvDb\Http\CacheClient;

if(isset($_GET['q']) && $_GET['q'] != "")
{
	$tvdb_cache      = new FilesystemCache(TVDB_CACHE);
	$tvdb_httpClient = new CacheClient($tvdb_cache, 604800);
	
	$tvdb = new Client(TVDB_URL, TVDB_API_KEY);
	$tvdb->setHttpClient($tvdb_httpClient);

	$serverTime = $tvdb->getServerTime();
	// Search for a show
	$data = $tvdb->getSeries($_GET['q']);

	echo json_encode($data);
} else {
	echo json_encode(array("error" => "no query"));
}