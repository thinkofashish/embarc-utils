<?php
require_once('/var/www/embarc-utils/php/sessions.php');
$session = new SESSIONS();
$session->check();
$session->isModuleAuthorized("1");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <title>Courier - DHL</title>
    <link href="/embarc-utils/css/normalize.css" rel="stylesheet">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	<link href="/embarc-utils/css/custom_style.css" rel="stylesheet">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>   
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script src="/embarc-utils/js/jquery.validate.min.js"></script>
	<script>window.eu = { 'id': "courier_dhl" }</script>
    <script src="/embarc-utils/js/common.js"></script>
    <script src="/embarc-utils/js/courier.js"></script>

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
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                <a class="navbar-brand" href="/embarc-utils/dashboard.php">
                        <!--<img src="/embarc-utils/images/logo.png" class="img-responsive img-resize-small" />-->
                        Embarc
				</a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse nav-collapse-scrollable">            	
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="/embarc-utils/courier/courier_dhl.php">DHL</a></li>
                        <li><a href="/embarc-utils/courier/settings_dhl.php">Preferences</a></li>
                    </ul>
					<ul class="nav navbar-nav navbar-right">
                        <li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
                    </ul>
            </div>
    	</div>
    </nav>
    <div>
    <div class="containt container">
		<div class="row">
			<h3>Calculate cost of courier using DHL</h3>
			<hr>
		</div>

		<div class="row">
			<form class="form-horizontal" role="form" id="packageDetailsForm">
			
				<div class="alert alert-danger" id="errorMessage-1"></div>
				
				<div class="form-group">
					<label class="col-lg-4 control-label" for="weight">Weight</label>
					<div class="col-lg-4">
						<div class="input-group">
							<input type="tel" class="form-control" id="weight" name="weight" placeholder="Weight" />	
							<span class="input-group-addon">kgs</span>
						</div>                 					
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="country">Country</label>
					<div class="col-lg-4">
						<select id="country" class="form-control" name="country"></select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="type">Type</label>
					<div class="col-lg-4">
						<select id="type" class="form-control" name="type"></select>
					</div>
				</div>
				<div class="form-group">
				<div class="col-lg-offset-4 col-lg-4 margin-bottom text-center">
					<button type="submit" class="btn btn-primary btn-block">Calculate</button>
				</div>
				</div>
			</form>
		</div>
        <div id="result"></div>
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