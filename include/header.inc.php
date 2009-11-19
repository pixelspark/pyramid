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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- Pyramid - an online competition system for large groups. Copyright (C) 2009, Tommy van der Vorst (pixelspark.nl) -->
<html>
  <head>
    <title><?php echo $Config['title'] ?>: <?php echo $title ?></title>
    <link rel="stylesheet" href="<?php echo $Base ?>/default.css" type="text/css" />
    <script type="text/javascript" src="<?php echo $Base ?>/jquery.js"></script>
  </head>
  
  <body>
    <!--[if lt IE 7]>
    <div style="background-color:red;font-weight:bold;color:white;">Hey! Je gebruikt een oude, stuk onveiligere
    versie van Internet Explorer. Download de nieuwe bij <a href="http://		www.microsoft.com">Microsoft</a> of download 
    meteen <a href="http://www.getfirefox.com">Firefox</a>. Mogelijk werkt de planner nu niet zo goed...</div>
    <![endif]-->         
    <div class="links">
    	<?php foreach($Config['links'] as $k=>$v) { ?>
			<a href="<?php echo $v ?>"><?php echo $k ?></a>
		<?php } ?> 
	</div> 
