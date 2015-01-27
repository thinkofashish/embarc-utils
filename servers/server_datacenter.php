<?php
require_once('/var/www/embarc-utils/php/sessions.php');
$session = new SESSIONS();
$session->check();
$session->isModuleAuthorized("4");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <title>Servers - Datacenters</title>
    <link href="/embarc-utils/css/normalize.css" rel="stylesheet">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.0/css/font-awesome.css" rel="stylesheet">
    <link href="/embarc-utils/css/custom_style.css" rel="stylesheet">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>   
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="/embarc-utils/js/common.js"></script>
	<script src="/embarc-utils/js/servers.js"></script>
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
                    </button>
                <a class="navbar-brand" href="/embarc-utils/dashboard.php">
                        Embarc
                    </a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse nav-collapse-scrollable">
            	
                    <ul class="nav navbar-nav">                        
                        <li><a href="/embarc-utils/servers/server_list.php">List</a></li>
                        <li><a href="/embarc-utils/servers/server_add.php">Add</a></li>
                        <li><a href="/embarc-utils/servers/server_pref.php">Preferences</a></li>
                        <li class="active"><a href="#">Datacenter</a></li>
					</ul>
                    <ul class="nav navbar-nav navbar-right">                      
                        <li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
                    </ul>                
            </div>
    	</div>
    </nav>
    <div class="containt container">
		<div class="alert alert-danger" id="errorMessage-1"><strong>Oh snap!</strong> An error occurred, please try again. </div>
		<div class="alert alert-danger" id="errorMessage-2"><strong>Bummer!</strong> Fields marked in red are required. </div>
    	<div class="alert alert-success" id="successMessage-1"><strong>Well done!</strong> Your preferences have been saved successfully. </div>
		<form class="form-horizontal" role="form" id="datacentresForm">
		  	<div class="row">
                <h3>Datacenter Details</h3>
                <hr>
			</div>
		 	 <div class="form-group">
                <label class="col-lg-4 control-label" for="name">Name</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="name" name="name" required />
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label" for="address">Address</label>
                <div class="col-lg-4">
                    <textarea class="form-control" id="address" name="address"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label" for="url">Support URL</label>
                <div class="col-lg-4">
				<div class="input-group">
					<span class="input-group-addon">http://</span>
                    <input type="text" class="form-control" id="url" name="url" required />
                </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label" for="phone">Phone Number</label>
                <div class="col-lg-4">
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>
            </div>                     
	        <div class="col-lg-offset-4 col-lg-4 margin-bottom">
                <button type="submit" class="btn btn-default">Save</button>
            </div>

        </form>
</div>
<div id="push"></div>
</div>
<footer>
<div id="footer">
	<div class="bs-footer">
    	<div class="container">
        	<div class="row">This work is licensed under the <a href="http://creativecommons.org/licenses/by-sa/3.0/">CC By-SA 3.0</a>, without all the cruft that would otherwise be
            put at the bottom of the page.</div>
        </div>
    </div>
   </div>
</footer>
                
    
</body>
</html>