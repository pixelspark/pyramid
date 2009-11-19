<?php
/*************************************************************************************
	Pyramid - an online competition system for large groups 
    Copyright (C) 2009, Tommy van der Vorst (tommy at pixelspark dot nl)
    All rights reserved.

	Redistribution and use in source and binary forms, with or without modification,
	are permitted provided that the following conditions are met:
	
	* Redistributions of source code must retain the above copyright notice, this 
	  list of conditions and the following disclaimer.
	* Redistributions in binary form must reproduce the above copyright notice, this
	  list of conditions and the following disclaimer in the documentation and/or 
	  other materials provided with the distribution.
	* Neither the name of the original developer(s) nor the names of its contributors
	   may be used to endorse or promote products derived from this software without 
	   specific prior written permission.
	
	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE 
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
	ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*************************************************************************************/
require_once("include/page.inc.php");

$Action = Get("action");
if($Action=="uitdagen") {
	$userid = RequireInput("username");
	$password = RequireInput("password");
	$playerbid = RequireInput("playerb");
	RequireValidPassword($userid, $password);	
	
	Query("UPDATE %%players AS pa, %%players AS pb SET pa.player_uitgedaagd=pb.player_id, pb.player_uitgedaagd=pa.player_id WHERE pa.player_id=? AND pb.player_id=? AND pa.player_uitgedaagd=0 AND pb.player_uitgedaagd=0 AND (pa.player_level=pb.player_level OR pa.player_level=pb.player_level+1)", $userid, $playerbid);
}
else if($Action=="uitslaginvoeren") {
	$userid = RequireInput("username");
	$password = RequireInput("password");
	$scorea = RequireInput("scorea");
	$scoreb = RequireInput("scoreb");
	if(!is_numeric($scorea) || !is_numeric($scoreb) || $scorea < 0 || $scoreb < 0) {
		Error("Ongeldige score ingevoerd");	
	}
	RequireValidPassword($userid, $password);	
	
	Query("START TRANSACTION");
	$levelChange = "";
	if($scorea<$scoreb) {
		$levelChange = ", pa.player_level=IF(pa.player_level>pb.player_level, pa.player_level, pb.player_level), pb.player_level=IF(pa.player_level<pb.player_level, pa.player_level, pb.player_level)";
	}
	else if($scorea>$scoreb) {
		$levelChange = ", pa.player_level=IF(pa.player_level<pb.player_level, pa.player_level, pb.player_level), pb.player_level=IF(pa.player_level>pb.player_level, pa.player_level, pb.player_level)";
	}
	
	Query("INSERT INTO %%games (game_playera, game_playerb, game_scorea, game_scoreb, game_date) VALUES (?,(SELECT player_uitgedaagd FROM %%players WHERE player_id=?),?,?,NOW())", $userid, $userid, $scorea, $scoreb);
	
	Query("UPDATE %%players AS pa, %%players AS pb SET pa.player_uitgedaagd=0, pb.player_uitgedaagd=0 ".$levelChange." WHERE pa.player_id=? AND pb.player_id=pa.player_uitgedaagd AND pb.player_uitgedaagd=pa.player_id AND (pa.player_level=pb.player_level OR ABS(pa.player_level-pb.player_level)<2)", $userid);
	Query("COMMIT");
		
}

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
</script>

<script type="text/javascript" src="pyramid.js"></script>

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
				echo "<div id=\"player-".$player->player_id."\" class=\"player ".($player->player_uitgedaagd!=0?"uitgedaagd":"")."\"><div class=\"wrapper\">";
				echo $player->player_name;
				echo "</div></div>";
			}
			
			if($numPlayersInThisLayer==$expectedInThisLayer || $a == count($Players)-1) {
				echo "</div>\r\n";	
			}
		}
		?>
		<div class="forms">
			<div class="header">Control panel</div>
			<form action="." method="post">
				<input type="hidden" name="action" id="post_action" value="none" />
				<div style="margin:5px;">
					Naam: 
					<select id="username" name="username" style="width:90px;" onclick="hideControlPanel();">
						<option></option>
						<?php
						for($a=0;$a<count($Players);$a++) {
							$player = $Players[$a];
							echo "<option value=\"".$player->player_id."\">".$player->player_name."</option>";	
						}
						?>
					</select>
				
					Intermate-wachtwoord: <input type="password" name="password" style="width:90px;" />
					
					<input type="button" onclick="showControlPanel();return false;" value="OK" class="button" />
					<a href="#" onclick="openRegisterForm();">Nog niet ingeschreven?</a>
					
					<div class="register-info" style="display:none;">
						Heb je je nog niet ingeschreven in de piramide? Mail dan de naam van je Intermate-account en eventueel een gewenste
						spelersnaam naar de sportcommissie. Als je nog geen foto hebt ingesteld op de Intermate-website, kun je dat ook meteen
						doen. De sportcommissie zal je dan zo snel mogelijk onderaan de piramide toevoegen. Schrijf je dus snel in, want hoe later
						je inschrijft, hoe meer spelers er boven je staan...
					</div>
				</div>
			
				<div id="actions" style="display:none;">
					<input type="submit" value="Bevestigen" class="button" />
				
					<div class="uitdagen">
						<b>Andere speler uitdagen</b>: 
						<select name="playerb" id="otherplayers" style="width:90px;">
						</select>
						<span class="explanation">Als je een andere speler hebt uitgedaagd, kun je pas weer iemand anders uitdagen als je de score voor deze wedstrijd hebt ingevuld. Degene die je uitdaagt krijgt automatisch een mailtje met je uitdaging.</span>
					</div>
						
					<div class="uitslaginvoeren">
						<b>Uitslag invoeren</b>: 
						<span class="username"></span>
						<input type="text" value="0" name="scorea" style="width:50px;" /> - <input type="text" value="0" name="scoreb" style="width:50px;" />
						<span id="uitdager"></span>
						<span class="explanation">Slechts &eacute;&eacute;n van de twee spelers hoeft de score in te vullen. De andere speler wordt op de hoogte gesteld van de ingevulde score.</span>
					</div>
					
					
				</div>
			
		</form>
	</div>

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