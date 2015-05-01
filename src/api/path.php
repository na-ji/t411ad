<?php

require_once __DIR__.'/../../bootstrap.php';

use Moinax\TvDb\Client;
use Moinax\TvDb\Http\Cache\FilesystemCache;
use Moinax\TvDb\Http\CacheClient;

if(isset($_GET['path']) && $_GET['path'] != "")
{
	$directories = array();
	foreach (scandir($_GET['path']) as $handle) {
		if (is_dir($_GET['path'].'/'.$handle) && is_writable($_GET['path'].'/'.$handle)) {
			$directories[] = $handle;
		}
	}

	echo json_encode($directories);
} else {
	echo json_encode(array("error" => "no query"));
}