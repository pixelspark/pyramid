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
require_once("config.inc.php");

$DB = mysql_connect($Config["db.host"], $Config["db.user"], $Config["db.password"]);
mysql_select_db($Config["db.name"], $DB);

function RequireValidPassword($username, $password) {
	if(!($username==$password)) {
		Error("Ongeldig wachtwoord; probeer het opnieuw!");	
	}	
}

function RequireInput($paramName) {
	if(!array_key_exists($paramName,$_REQUEST)) {
		Error("Parameter '".htmlentities($paramName)."' was niet ingesteld");	
	}	
	return Get($paramName);
}

function Get($paramName) {
	if(get_magic_quotes_gpc()) {
		return stripslashes($_REQUEST[$paramName]);	
	}
	return $_REQUEST[$paramName];	
}

function Query($q, $v=array()) {
	global $DB;
	global $Config;
	
	$q = str_replace("%%",$Config["db.prefix"],$q);
	
	if(!is_array($v)) {
		if(func_num_args()>1) {
			$v = array();
			for($a=1;$a<func_num_args();$a++) {
				$v[] = func_get_arg($a);
			}
		}
		else {
			$v = array();
		}
	}

	if(count($v)>0) {
		$qr = explode("?",$q);
		$q = "";
		foreach($qr as $index=>$value) {
			if(isset($v[$index])) {
				$q .= $value." '".mysql_escape_string($v[$index])."' ";
			}
			else {
				$q .= $value;
			}
		}
	}
	
	$result = mysql_query($q, $DB);
	
	if(mysql_errno($DB)) {
           Error(mysql_error($DB)."<br/><br/>Query: <pre>$q</pre>");
	}
	
	if(is_resource($result)) {
		$data = array();
		$n = mysql_num_rows($result);
		for($a=0;$a<$n;$a++) {
			 $data[] = mysql_fetch_object($result);
		}
		
		return $data;
	}
	return array();
}

function Head($title,$links=array()) {
	global $Config;
	$Base = $Config["base"];
	$title = htmlentities($title);
	require_once("header.inc.php");
}

function Foot() {
	global $Base;
	require_once("footer.inc.php");
}

function Error($msg) {
    Head("Fout");
    echo "<p>Fout: $msg</p> <a href=\".\">Terug</a>";
    Foot();
    die;
}

function JSONSerialize($data) {
    $json = "";
    if(is_array($data)) {
        $json .= "";
        $elements = "[ ";
        foreach($data as $v) {
            $elements .= JSONSerialize($v);
            $elements .= " ,";
        }
        $json .= substr($elements, 0, strlen($elements)-1);
        $json .= " ]";
    }
    else if(is_object($data)) {
        $json .= "{";
         $elements = "";
        foreach($data as $k=>$v) {
            $elements .= $k . ": ". JSONSerialize($v);
            $elements .= " ,";
        }
        $json .= substr($elements, 0, strlen($elements)-1);
        $json .= "}";
    }
    else {
        $json .= "\"".addslashes($data)."\"";
    }
    return $json;
}


?>