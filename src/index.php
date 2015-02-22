<?php

require_once __DIR__.'/../bootstrap.php';

use Moinax\TvDb\Client;

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

$flashBags = array();

if (isset($_POST['path']) && isset($_POST['tvdbID']) && intval($_POST['tvdbID']) > 1)
{

	$verification = $em->getRepository("TVShow")->findOneByTvdbId(intval($_POST['tvdbID']));

	if (null !== $verification)
	{
		$flashBags[] = array('warning' => 'Cette série est déjà ajoutée !');
	} else {
		$tvdb = new Client(TVDB_URL, TVDB_API_KEY);

		$tv_show = $tvdb->getSerie(intval($_POST['tvdbID']));

		//var_dump($tv_show);

		$TVShow = new TVShow();
		$TVShow
			->setName($tv_show->name)
			->setBanner($tv_show->banner)
			->setImdbId($tv_show->imdbId)
			->setTvdbId($tv_show->id)
			->setZap2ItId($tv_show->zap2ItId)
			->setDownloadPath($_POST['path'])
		;
		$em->persist($TVShow);
		$episodes = $tvdb->getSerieEpisodes($tv_show->id);
		$maintenant = new \DateTime();
		foreach ($episodes['episodes'] as $db_episode) {
			$episode = new Episode();
			$episode
				->setName($db_episode->name)
				->setNumber($db_episode->number)
				->setSeason($db_episode->season)
				->setTvdbId($db_episode->id)
				->setFirstAired($db_episode->firstAired)
				->setThumbnail($db_episode->thumbnail)
				->setTvshow($TVShow)
				->setDownloaded((isset($_POST['marquerCommeTelecharger']) ? (null === $db_episode->firstAired ? false : $db_episode->firstAired <= $maintenant) : false))
			;
			$em->persist($episode);
		}
		$em->flush();
		$flashBags[] = array('success' => $tv_show->name.' a bien été ajoutée !');
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>T411 Auto Downloader Configurator</title>
	<link href="//maxcdn.bootstrapcdn.com/bootswatch/3.3.2/superhero/bootstrap.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
	<style type="text/css">
	a.tvshowLink, a.tvshowLink:hover {
		text-decoration: none;
	}
	li.liAutocomplete:hover {
		cursor:pointer;
	}
	</style>
</head>
<body>
	<div class="navbar navbar-default navbar-static-top">
		<div class="container">
			<div class="navbar-header">
				<a href="" class="navbar-brand">T411AD</a>
			</div>
			<div class="navbar-collapse collapse" id="navbar-main" role="tabpanel">
				<ul class="nav navbar-nav" role="tablist">
					<li class="active">
						<a href="#list" aria-controls="list" role="tab" data-toggle="tab">Liste</a>
					</li>
					<li>
						<a href="#add" aria-controls="add" role="tab" data-toggle="tab">Ajouter</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
    <div class="container" style="margin-top: 50px;">
    	<?php
    		if (count($flashBags) > 0)
    		{
    			foreach ($flashBags as $alert) {
    				foreach ($alert as $level => $message) {
    					echo '<div class="alert alert-dismissible alert-'.$level.'" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.$message.'</div>';
    				}
    			}
    		}
    	?>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane fade in active" id="list">
				<h1>Liste des séries en téléchargement</h1>
				<?php
					$tv_shows = $em->getRepository("TVShow")->findAll();
					$s = (count($tv_shows) > 1 ? 's' : '');

					echo '<h3>'.count($tv_shows).' série'.$s.' enregistrée'.$s.'</h3>';
					if (count($tv_shows) > 0)
					{
						foreach ($tv_shows as $tv_show) {
							echo $tv_show->getName().'<br />';
						}
					} 
				?>
				<h3>Prochains téléchargements</h3>
				<?php
					$episodes = $em->getRepository("Episode")->getNextDownloads();

					if (count($episodes) > 0)
					{
						foreach ($episodes as $episode) {
							echo $episode->getFirstAired()->format('d/m/Y').' : '.$episode->getTvshow()->getName().' - '.$episode->getName().'<br />';
						}
					} else {
						echo 'Aucun épisode prévu.';
					}
				?>
			</div>
			<div role="tabpanel" class="tab-pane fade" id="add">
				<h1>Ajouter une série</h1>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Recherche" id="query">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit" id="search">
                        	<i class="glyphicon glyphicon-search"></i>
                        </button>
                    </div>
                </div>
                <i id="loading" class="fa fa-spinner fa-spin fa-2x" style="color: black;float: right;margin: -34px 54px 0 0;z-index: 3;position: relative;display: none;"></i>
                <div id="results" style="margin-top:30px;" class="row">

                </div>
				<div class="modal fade" id="myModal">
					<div class="modal-dialog">
						<div class="modal-content">
							<form method="post">	
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
									</button>
									<h4 class="modal-title">TV Show</h4>
								</div>
								<div class="modal-body">
									<p></p>
									<div class="form-group">
										<label for="chemin">Chemin de sauvegarde</label>
										<input type="text" class="form-control" id="chemin" name="path" value="<?php echo DOWNLOAD_PATH; ?>">
										<div id="autocomplete" style="display:none;width: 100%; height: 250px; max-width: 558px; position: absolute; z-index: 2; overflow: overlay; background: white;color: black;">
											<ul>
												<li>.</li>
												<li>..</li>
											</ul>
										</div>
									</div>
									<div class="checkbox">
										<label>
											<input type="checkbox" name="marquerCommeTelecharger" checked>Marquer les épisodes déjà sortis comme téléchargés
										</label>
									</div>
									<input type="hidden" id="tvdbID" name="tvdbID" value="0">
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
									<button type="submit" class="btn btn-primary" id="submit">Ajouter celle-là</button>
								</div>
							</form>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
			</div>
		</div>
    </div>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
	<script type="text/javascript">
		var tvshows;
		function search()
		{
			var query = $('#query').val();
			if (query.length > 0)
			{
				$('#loading').show();
				$.getJSON('api/search.php?q=' + query, function(response)
				{
					console.log(response);
					if (typeof response === 'object' && response.error)
					{
						$('#results').text('Erreur dans la recherche');
					} else {
						var c = response.length,
							html = '<div class="col-sm-12"><h3>' + c + ' résultat' + (c > 1 ? 's' : '') + '</h3></div>';
						tvshows = response;
						for(var i = 0; i < c; i++)
						{
							html += '<div class="col-sm-12" style="margin-bottom: 15px;"><a class="tvshowLink" indice="' + i + '" href="#"><img src="http://thetvdb.com/banners/' + response[i].banner + '" style="width:758px;height:140px;background-color: grey;" />&nbsp;&nbsp;&nbsp;' + response[i].name + '</a></div>';
						}
						$('#results').html(html);
						$('a.tvshowLink').click(function(e) {
							e.preventDefault();
							var i = $(this).attr('indice');
							$('#tvdbID').val(tvshows[i].id);
							$('h4.modal-title').text(tvshows[i].name);
							$('div.modal-body p').text(tvshows[i].overview);
							$('#myModal').modal();
						});
					}
					$('#loading').hide();
				});
			}
		}
		function autocomplete(){
			var path = $('#chemin').val();
			$.getJSON('api/path.php?path=' + path, function(response) {
				console.log(response);
				if (typeof response === 'object' && response.error)
				{
					$('#autocomplete ul').text('Erreur dans la recherche');
				} else {
					var c = response.length, html = '';
					if (c > 0) {
						for(var i = 0; i < c; i++){
							html += '<li class="liAutocomplete">' + response[i] + '</li>';
						}
						$('#autocomplete ul').html(html);
						$('#autocomplete').show('slow');
						$('li.liAutocomplete').click(function(e) {
							e.preventDefault();
							var value = $(this).text();
							if (value === ".")
							{
								$('#chemin').trigger('focusout');
							} else if(value === "..")
							{
								var folders = $('#chemin').val().split("/");
								var newPath = '';
								for (var i = 0, c = folders.length - 1; i < c; i++) {
									newPath	+= folders[i] + '/';
								}
								console.log(newPath);
								$('#chemin').val(newPath.slice(0, -1));
								autocomplete();
							} else {
								$('#chemin').val($('#chemin').val() + '/' + value);
								autocomplete();
							}

						})
					}
				}
			});
		}
		$('#query').keyup(function(e){
		    if(e.keyCode == 13)
		    {
		        search();
		    }
		});
		$('#search').click(function(e) {
			e.preventDefault();
			search();
		});
		$('#chemin').focus(function(e) {
			e.preventDefault();
			console.log('coucou');
			autocomplete();
		});
		$('#chemin').focusout(function(e) {
			e.preventDefault();
			$('#autocomplete').hide('slow');
		});
	</script>
</body>
</html>