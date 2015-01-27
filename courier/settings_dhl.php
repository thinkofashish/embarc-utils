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
    <title>Courier - Preferences</title>
    <link href="/embarc-utils/css/normalize.css" rel="stylesheet">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	<link href="/embarc-utils/css/custom_style.css" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script>window.eu = { 'id': "courier_preferences" }</script>
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
                        <li><a href="/embarc-utils/courier/courier_dhl.php">DHL</a></li>
                        <li class="active"><a href="/embarc-utils/courier/settings_dhl.php">Preferences</a></li>
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
			<h3>Customize calculation parameters</h3>
			<hr>
		</div>
		
		<div class="row">
			<div id="messages"></div>
			<form class="form-horizontal" id="settingsForm">
				
				<div class="alert alert-success" id="successMessage-1"><strong>Great!</strong> You have successfully saved all settings.</div>
				<div class="alert alert-danger" id="errorMessage-1"><strong>Oh snap!</strong> There was an while saving settings.</div>
				
				<div class="form-group">
					<label class="col-lg-4 control-label" for="d_value">Dollar Value</label>
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">₹</span>
							<input type="text" class="form-control" id="dollarValue" name="dollarValue" placeholder="Dollar Value">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="e_value">Euro Value</label>
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">₹</span>
							<input type="text" class="form-control" id="euroValue" name="euroValue" placeholder="Euro Value">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="f_surcharge">Fuel Surcharge</label>
					<div class="col-lg-4">
						<div class="input-group">
							<input type="text" class="form-control" id="fuelSurcharge" name="fuelSurcharge" placeholder="Fuel Surcharge">
							<span class="input-group-addon">%</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="misc">Miscellaneous</label>
					<div class="col-lg-4">
						<div class="input-group">
							<input type="text" class="form-control" id="misc" name="misc" placeholder="Miscellaneous">
							<span class="input-group-addon">%</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="c_cost">Clearance Cost</label>
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">₹</span>
							<input type="text" class="form-control" id="clearanceCost" name="clearanceCost" placeholder="Clearance Cost">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="c_cost">USD Handling Charges</label>
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" class="form-control" id="handlingCharges_USD" name="handlingCharges_USD" placeholder="USD Handling Charges">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="c_cost">USD Minimum Billing</label>
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="text" class="form-control" id="minBilling_USD" name="minBilling_USD" placeholder="USD Minimum Billing">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="c_cost">EUR Handling Charges</label>
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">&euro;</span>
							<input type="text" class="form-control" id="handlingCharges_EUR" name="handlingCharges_EUR" placeholder="EUR Handling Charges">
						</div>
					</div>     
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="c_cost">EUR Minimum Billing</label>
					<div class="col-lg-4">
						<div class="input-group">
							<span class="input-group-addon">&euro;</span>
							<input type="text" class="form-control" id="minBilling_EUR" name="minBilling_EUR" placeholder="EUR Minimum Billing">
						</div>
					</div>
				</div>
				<div class="col-lg-offset-4 col-lg-4 margin-bottom text-center">
					<button type="button" class="btn btn-success" id="saveButton">Save</button>
					<button type="button" class="btn btn-default" onclick="gotoPage('/embarc-utils/courier/courier_dhl.php');">Cancel</button>
				</div>
			</form>
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