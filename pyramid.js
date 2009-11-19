function removeAllChildNodesOf(elm) {
	if(elm.hasChildNodes()) {
	    while(elm.childNodes.length >= 1) {
	    	elm.removeChild(elm.firstChild);  
	    } 
	}	
}

function findPlayerById(i) {
	for(var w in players) {
			if(players[w].player_id==i) {
				return players[w];
			}
		}	
}

function selectPlayer(myself) {
	$("div.player").removeClass("selected");
	$("div.player").removeClass("won");
	$("div.player").removeClass("lost");
	$("div.player").removeClass("undecided");
	$("div#player-"+myself.player_id).addClass("selected");	
 		
	$(document.getElementById("player-naam")).text(myself.player_name);	
	
	// Games laten zien
	var gamesTable = document.getElementById('games');
	removeAllChildNodesOf(gamesTable);
	
	for(var w in games) {
		var game = games[w];
		if(game.game_playera==myself.player_id || game.game_playerb==myself.player_id) {
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
		if(data.player==myself.player_id) {
			$(document.getElementById("games-gewonnen")).text(data.times + " game(s) (+"+data.totalscore+" punten)");
			doelsaldo = data.totalscore;
		}		
	}
	
	$(document.getElementById("games-verloren")).text("0");
	for(var w in player_lost) {
		var data = player_lost[w];
		if(data.player==myself.player_id) {
			$(document.getElementById("games-verloren")).text(data.times + " game(s) (-"+data.totalscore+" punten)");
			doelsaldo -= data.totalscore;
		}		
	}
	
	$(document.getElementById("doelsaldo")).text(doelsaldo);
	
	// Show better and worse players
	for(var w in winners) {
		var data = winners[w];
		if(data.winner==myself.player_id) {
			$(document.getElementById("player-"+data.loser)).addClass("won");	
		}
		else if(data.loser==myself.player_id) {
			$("div#player-"+data.winner).addClass("lost");	
		}
	}
	
	if(myself.player_uitgedaagd!=0) {
		$("div#player-"+myself.player_uitgedaagd).addClass("undecided");	
	}	
}

function openRegisterForm() {
	$(".register-info").show("slow");	
}

 $(document).ready(function(){
 	$("div.player").mouseover(function() {
 		var myself = findPlayerById(this.id.substr(7));
 		selectPlayer(myself);
  	});
  	
  	$("div.player").click(function() {
  		var myID = this.id.substr(7);
  		var playerList = document.getElementById('username');
  		hideControlPanel();
  		playerList.value = myID;
  	});
 });
 
 function hideControlPanel() {
 	$("#actions").hide();
 }
 
 function showControlPanel() {
 	$(".register-info").hide();
 	var otherPlayerList = document.getElementById('otherplayers');
 	var playerList = document.getElementById('username');
 	removeAllChildNodesOf(otherPlayerList);
 	var me = findPlayerById(playerList.value);
 	hideControlPanel();
 	$(".username").text(me.player_name);
 	selectPlayer(me);
 	var kanUitdagen = (me.player_uitgedaagd==0);
 	if(kanUitdagen) {
 		document.getElementById("post_action").value = "uitdagen";
	 	for(var p in players) {
	 		if(players[p].player_level == (me.player_level-1) && players[p].player_uitgedaagd==0) {
	 			var option = document.createElement("option");	
	 			option.value = players[p].player_id;
	 			option.appendChild(document.createTextNode(players[p].player_name));
	 			otherPlayerList.appendChild(option);
	 		}	
	 	}
	 	
	 	otherPlayerList.appendChild(document.createElement("option"));
	 	
	 	for(var p in players) {
	 		if(players[p].player_level == me.player_level && players[p].player_id!=me.player_id && players[p].player_uitgedaagd==0) {
	 			var option = document.createElement("option");	
	 			option.value = players[p].player_id;
	 			option.appendChild(document.createTextNode(players[p].player_name));
	 			otherPlayerList.appendChild(option);
	 		}	
	 	}
 	}
 	else {
 		document.getElementById("post_action").value = "uitslaginvoeren";
 		var uitdager = findPlayerById(me.player_uitgedaagd);
 		$("#uitdager").text(uitdager.player_name);
 	}
 	$("#actions .uitdagen").css("display", kanUitdagen ? "inline" : "none");
 	$("#actions .uitslaginvoeren").css("display", kanUitdagen ? "none" : "inline");
 	
 	$("#actions").show("fast");
 }