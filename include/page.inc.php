<?php
$DB = mysql_connect("", "", "");
mysql_select_db("pyramid",$DB);

$Base = "/pyramid";
$TablePrefix = "pyramid_";

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
	
	if(mysql_errno($DB)) {
           Error(mysql_error($DB));
	}
	
	$data = array();
	$n = mysql_num_rows($result);
	for($a=0;$a<$n;$a++) {
		 $data[] = mysql_fetch_object($result);
	}
	return $data;
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
    echo "<p>Fout: $msg</p>";
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
