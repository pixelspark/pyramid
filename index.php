<?php
require_once("include/page.inc.php");

Head("Overzicht");
$Players = Query("SELECT * FROM %%players ORDER BY player_level ASC");
$Winners = Query("SELECT winner, loser, COUNT(game_id) AS times FROM %%winners GROUP BY winner, loser");
$PlayerWon = Query("SELECT winner AS player, COUNT(*) AS times, SUM(winner_score) AS totalscore FROM %%winners GROUP BY winner");
$PlayerLost = Query("SELECT loser AS player, COUNT(*) AS times, SUM(winner_score) AS totalscore FROM %%winners GROUP BY loser");
$Games = Query("SELECT game_playera, game_playerb, playera.player_name AS aname, playerb.player_name AS bname, game_scorea, game_scoreb, DATE_FORMAT(game_date,'%d-%m-%Y') AS game_date FROM %%games LEFT JOIN %%players AS playera ON playera.player_id=game_playera LEFT JOIN %%players AS playerb ON playerb.player_id=game_playerb ORDER BY game_date ASC");
?>

<script type="text/javascript">
	var winners = <?php echo JSONSerialize($Winners); ?>;
	var player_won = <?php echo JSONSerialize($PlayerWon) ?>;
	var player_lost = <?php echo JSONSerialize($PlayerLost) ?>;
	var players = <?php echo JSONSerialize($Players); ?>;
	var games = <?php echo JSONSerialize($Games); ?>;

	 $(document).ready(function(){
	 	$("div.player").mouseover(function() {
	 		$("div.player").removeClass("selected");
	 		$("div.player").removeClass("won");
	 		$("div.player").removeClass("lost");
	 		$(this).addClass("selected");	
	 		
	 		var playerID = this.id.substr(7);
	 		
	 		// Naam ophalen
	 		for(var w in players) {
	 			if(players[w].player_id==playerID) {
	 				$(document.getElementById("player-naam")).text(players[w].player_name);	
	 			}
	 		}
	 		
	 		// Games laten zien
	 		var gamesTable = document.getElementById('games');
	 		if(gamesTable.hasChildNodes()) {
			    while(gamesTable.childNodes.length >= 1) {
			        gamesTable.removeChild(gamesTable.firstChild);  
			    } 
			}
			
			for(var w in games) {
				var game = games[w];
				if(game.game_playera==playerID || game.game_playerb==playerID) {
					var tr = document.createElement("TR");
					var tda = document.createElement("TD");	
					var tdb = document.createElement("TD");	
					var tdd = document.createElement("TD");
					var tds = document.createElement("TD");	
					$(tda).addClass("game-player");
					$(tda).text(game.aname);
					$(tdb).addClass("game-player");
					$(tdb).text(game.bname);
					$(tdd).addClass("game-date");
					$(tdd).text(game.game_date);
					$(tds).addClass("game-score");
					$(tds).text(game.game_scorea+"-"+game.game_scoreb);
					tr.appendChild(tda);
					tr.appendChild(tdb);
					tr.appendChild(tdd);
					tr.appendChild(tds);
					gamesTable.appendChild(tr);
				}	
			}

	 		
	 		// Show stats
	 		var doelsaldo = 0;
	 		$(document.getElementById("games-gewonnen")).text("0");
	 		for(var w in player_won) {
	 			var data = player_won[w];
	 			if(data.player==playerID) {
	 				$(document.getElementById("games-gewonnen")).text(data.times + " game(s) (+"+data.totalscore+" punten)");
	 				doelsaldo = data.totalscore;
	 			}		
	 		}
	 		
	 		$(document.getElementById("games-verloren")).text("0");
	 		for(var w in player_lost) {
	 			var data = player_lost[w];
	 			if(data.player==playerID) {
	 				$(document.getElementById("games-verloren")).text(data.times + " game(s) (-"+data.totalscore+" punten)");
	 				doelsaldo -= data.totalscore;
	 			}		
	 		}
	 		
	 		$(document.getElementById("doelsaldo")).text(doelsaldo);
	 		
	 		// Show better and worse players
	 		for(var w in winners) {
	 			var data = winners[w];
	 			if(data.winner==playerID) {
	 				$(document.getElementById("player-"+data.loser)).addClass("won");	
	 			}
	 			else if(data.loser==playerID) {
	 				$("div#player-"+data.winner).addClass("lost");	
	 			}
	 		}
	 	});
	 });

</script>

<div class="content">
	<div class="pyramid">
		<?php
		$numPlayersInThisLayer = 0;
		$currentLayer = -1;
		$expectedInThisLayer = 0;
		for($a=0; $a<count($Players); $a++) {
			if($numPlayersInThisLayer>=$expectedInThisLayer) {
				$numPlayersInThisLayer = 0;
				$currentLayer++;
				$expectedInThisLayer++;
				echo "<div class=\"layer\">";	
			}
			$numPlayersInThisLayer++;
			
			$player = $Players[$a];
			if($player->player_level==$currentLayer) {
				echo "<div id=\"player-".$player->player_id."\" class=\"player ".($player->player_uitgedaagd==1?"uitgedaagd":"")."\"><div class=\"wrapper\">";
				echo $player->player_name;
				echo "</div></div>";
			}
			
			if($numPlayersInThisLayer==$expectedInThisLayer || $a == count($Players)-1) {
				echo "</div>\r\n";	
			}
		}
		?>
	</div>
	
	<div class="info">
		<div id="player-naam"></div>
		<table>
			<tr><td colspan="2"></td></td>
			<tr><td>Doelsaldo:</td><td><span id="doelsaldo"></span></td></tr>
			<tr><td>Gewonnen:</td><td><span id="games-gewonnen"></span></td></tr>
			<tr><td>Verloren:</td><td><span id="games-verloren"></span></td></tr>
		</table>
		<br/>
		<div class="header">Games gespeeld</div>
		<div id="games-played">
			<table cellspacing="0" cellpadding="0" id="games"><tr><td><em>Geen informatie beschikbaar</em></td></tr></table>
		</div>
		
	</div>
</div>

<?php Foot() ?>