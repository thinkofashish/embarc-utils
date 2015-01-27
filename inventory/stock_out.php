<?php
require_once('/var/www/embarc-utils/php/sessions.php');
$session = new SESSIONS();
$session->check();
$session->isModuleAuthorized("2");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <title>Inventory - Out</title>
    <link href="/embarc-utils/css/normalize.css" rel="stylesheet">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
	<link href="/embarc-utils/css/custom_style.css" rel="stylesheet">
	<link href="/embarc-utils/css/datepicker.css" rel="stylesheet">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>   
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/embarc-utils/js/bootstrap-datepicker.js"></script>
    <script src="/embarc-utils/js/common.js"></script>
	<script src="/embarc-utils/js/inventory.js"></script>
	<script src="/embarc-utils/js/jquery.validate.min.js"></script>

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
                        <li><a href="/embarc-utils/inventory/stock_in.php">Stock In</a></li>
                        <li class="active"><a href="/embarc-utils/inventory/stock_out.php">Stock Out</a></li>
                        <li><a href="/embarc-utils/inventory/stock_finder.php">Stock Finder</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">More <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                              <li><a href="/embarc-utils/inventory/preferences.php">Preferences</a></li>
                              <li><a href="/embarc-utils/inventory/clients.php">Clients</a></li>
                              <li><a href="/embarc-utils/inventory/trackers.php">Trackers</a></li>                              
                            </ul>
                          </li>  
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
			<h3>Move tracker out of stock</h3>
			<hr />
		</div>
		<div class="row">
			<div class="alert alert-danger" id="errorMessage-1"><strong>Oh snap!</strong> Change a few things up and try submitting again. </div>
			<div class="alert alert-danger" id="errorMessage-2"><strong>Bummer!</strong> This IMEI is not is stock. </div>
			<div class="alert alert-success" id="successMessage-1"><strong>Well done!</strong> Stock item checked out successfully. </div>
			
			<form class="form-horizontal" role="form" id="stockOutForm">        
				<div class="form-group">
					<label class="col-lg-4 control-label" for="out_invoice">Invoice #</label>
					<div class="col-lg-4">
						<input type="text" class="form-control" id="out_invoice" name="out_invoice" placeholder="Invoice Number" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="clientName">Client</label>
					<div class="col-lg-4">
						<select id="clientID" name="clientID" class="form-control" required></select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="dateOfSale">Date of Sale</label>
					<div class="col-lg-4">
						<input type="text" class="form-control datepicker" id="dateOfSale" name="dateOfSale" placeholder="Date of Sale">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="out_warranty">Warranty Provided</label>
					<div class="col-lg-4">
					   <div class="input-group">
							<input type="text" class="form-control" id="out_warranty" name="out_warranty" placeholder="Warranty">
							<span class="input-group-addon">Months</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="imei">IMEI</label>
					<div class="col-lg-4">
						<input type="hidden" id="id" name="id" />
						<input type="text" class="form-control" id="imei" name="imei" placeholder="IMEI" required>
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-4 control-label" for="serial">Tracker Serial</label>
					   <div class="col-lg-4">
			<input type="text" class="form-control" id="serial" name="serial" disabled placeholder="Tracker Serial" required>
			
		</div>
		</div>
				
				<div class="form-group">
					<label class="col-lg-4 control-label" for="model">Model</label>
					<div class="col-lg-4">
						<input type="text" id="model" class="form-control" disabled name="model" placeholder="Model" required>
					   
					</div>
				</div>            

				<div class="col-lg-offset-4 col-lg-4 margin-bottom">
					<button type="submit" class="btn btn-primary btn-block" id="saveStockButton" data-loading-text="Saving...">Check out</button>
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