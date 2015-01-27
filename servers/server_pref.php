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
    <title>Servers - Preferences</title>
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
                        <li class="active"><a href="#">Preferences</a></li>
                        <li><a href="/embarc-utils/servers/server_datacenter.php">Datacenter</a></li>
					</ul>
                    <ul class="nav navbar-nav navbar-right">                      
                        <li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
                    </ul>                
            </div>
    	</div>
    </nav>
    <div class="containt container">
		<div class="alert alert-danger" id="errorMessage-1"><strong>Oh snap!</strong> An error occurred, please try again. </div>
    	<div class="alert alert-success" id="successMessage-1"><strong>Well done!</strong> Your preferences have been saved successfully. </div>
		<form class="form-horizontal" role="form" id="preferencesForm">
		  	<div class="row">
                <h3>List</h3>
                <hr>
			</div>
		 	 <div class="form-group">
                <label class="col-lg-4 control-label" for="rInt">Auto refresh interval</label>
                <div class="col-lg-4">
                <div class="input-group">
                    <input type="tel" class="form-control" id="rInt" name="rInt" placeholder="0" />
                    <span class="input-group-addon">minutes</span>
                </div>
                </div>
            </div>
			 <div class="form-group">
                <label class="col-lg-4 control-label" for="tcInt">Trackers update interval</label>
                <div class="col-lg-4">
                <div class="input-group">
                    <input type="tel" class="form-control" id="tcInt" name="tcInt" placeholder="60" />
                    <span class="input-group-addon">minutes</span>
                </div>
                </div>
            </div>
            <div class="form-group">
            <div class="col-lg-offset-4 col-lg-4 margin-bottom">
				<div class="checkbox">
					<label>
						<input name="showDel" id="showDel" type="checkbox"> Show Delete button
					</label>
				</div>         
			</div>
			</div>
            
            <div class="form-group">
            <div class="col-lg-offset-4 col-lg-4 margin-bottom">
				<div class="checkbox">
					<label>
						<input name="cSort" id="cSort" type="checkbox"> Sort by company name
					</label>
				</div>         
			</div>
			</div>
			
			<div class="form-group">
            <div class="col-lg-offset-4 col-lg-4 margin-bottom">
				<div class="checkbox">
					<label>
						<input name="noKey" id="noKey" type="checkbox"> Do not prompt for secret key
					</label>
				</div>         
			</div>
			</div>
			
			<div class="form-group">
            <div class="col-lg-offset-4 col-lg-4 margin-bottom">
				<div class="checkbox">
					<label>
						<input name="noStatus" id="noStatus" type="checkbox"> Do not fetch server status
					</label>
				</div>         
			</div>
			</div>
			
            <div class="row">
                <h3>Add</h3>
                <hr>
			</div>
            <div class="form-group">
                <label class="col-lg-4 control-label" for="sw_version">Software Version</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="sw_version" name="sw_version" placeholder="4.*.*">
                </div>
            </div>			
			<div class="form-group">
                <label class="col-lg-4 control-label" for="port">Port</label>
                <div class="col-lg-4">
                    <input type="tel" class="form-control" id="port" name="port" placeholder="21000">
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label" for="hosted_at">Hosted At</label>
                <div class="col-lg-4">
					<select class="form-control" id="hosted_at" name="hosted_at"></select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-lg-4 control-label" for="user2_username">Secondary User</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="user2_username" name="user2_username" placeholder="Secondary User">
                </div>
            </div>
			<div class="form-group">
                <label class="col-lg-4 control-label" for="user2_password">Secondary User Password</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="user2_password" name="user2_password" placeholder="Secondary User Password">
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