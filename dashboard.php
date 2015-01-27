<?php
require_once('php/sessions.php');
$session = new SESSIONS();
$session->check();
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <title>Dashboard</title>
        <link href="/embarc-utils/css/normalize.css" rel="stylesheet">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-theme.min.css">
<link href="/embarc-utils/css/dashboard.css" rel="stylesheet">
<link href="/embarc-utils/css/custom_style.css" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/embarc-utils/js/common.js"></script>
	<script src="/embarc-utils/js/dashboard.js"></script>


    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

</head>
<body>
<div id="wrap">
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    	<div class="container">
        	<div class="navbar-header">
            	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
				</button>
                <a class="navbar-brand" href="#">
                        <!--<img src="/embarc-utils/images/logo.png" class="img-responsive img-resize-small" />-->
                        Embarc
				</a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse nav-collapse-scrollable">
				<ul class="nav navbar-nav navbar-right">
					<li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
				</ul>
            </div>
    	</div>
    </nav>
    <div>
    <div class="containt ">
        <div class="gallery" id="modulesContainer">
            
            <!--<div style="clear: both"></div>-->
        </div>
    </div>
    </div>
    <div id="push"></div>
</div>
<footer>
	<div id="footer">
		<div class="bs-footer">
			<div class="container">
				<div class="row">
                This work is licensed under the <a href="http://creativecommons.org/licenses/by-sa/3.0/">CC By-SA 3.0</a>, without all the cruft that would otherwise be
            put at the bottom of the page.
                </div>
			</div>
		</div>
	</div>
</footer>
</body>
</html>
