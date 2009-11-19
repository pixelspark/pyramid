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