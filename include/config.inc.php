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
$Config = array();

/* To install, edit the configuration values below. The scripts have been tested on 
MySQL 5.1.38, but any 5.* version or higher should work. The db.prefix setting can
be used to prefix table names whenever the pyramid uses a database that is shared with
another program or other installation of pyramid in the same database. */
$Config["db.user"] = "web";
$Config["db.password"] = "w3bw3b";
$Config["db.name"] = "pyramid";
$Config["db.host"] = "localhost";
$Config["db.prefix"] = "pyramid_";

/* The 'base' parameter sets the absolute URL at which the Pyramid installation can be
found. In most cases, you can set this to the URL at which the Pyramid directory is, 
starting with a forward slash. */
$Config["base"] = "/pyramid";
$Config["title"] = "Intermate Squashcompetitie";

$Config["links"] = array();
$Config["links"]["Intermate"] = "http://www.intermate.nl/";
$Config["links"]["Powered by PCWC"] = "http://www.intermate.nl/commissies/pcwc";
?>