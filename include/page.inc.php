<?php
$DB = mysql_connect("localhost", "web", "w3bw3b");
mysql_select_db("pyramid",$DB);

$Base = "/pyramid";
$TablePrefix = "pyramid_";

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
	global $TablePrefix;
	
	$q = str_replace("%%",$TablePrefix,$q);
	
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
	//echo "<code>".htmlentities($q)."</code>: ".mysql_affected_rows()." affected<hr/>";
	
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
	global $Base;
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