<?php
require_once('/var/www/embarc-utils/php/sessions.php');
$session = new SESSIONS();
$session->check();
$session->isModuleAuthorized("7");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <title>SMTP - Check</title>
    <link href="/embarc-utils/css/normalize.css" rel="stylesheet">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link href="/embarc-utils/css/custom_style.css" rel="stylesheet">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>   
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script src="/embarc-utils/js/jquery.validate.min.js"></script>
	<script>window.eu = { 'id': "smtp_check" }</script>
    <script src="/embarc-utils/js/common.js"></script>
	<script src="/embarc-utils/js/smtp.js"></script>

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
                        <li class="active"><a href="#">Check</a></li>
					</ul>
                    <ul class="nav navbar-nav navbar-right">                      
                        <li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
                    </ul>                
            </div>
    	</div>
    </nav>
    <div class="containt container">
		<div class="row">
			<h3>Check your SMTP server</h3>
			<hr>
		</div>
		<div class="row">
			<form class="form-horizontal" role="form" id="smtpForm">
				 <div class="form-group">
					<label class="col-lg-4 control-label" for="host">Host</label>
					<div class="col-lg-4">
						<input type="text" class="form-control" id="host" name="host" placeholder="Specify mail server" required />
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-lg-4 control-label" for="alerts">Port</label>
					<div class="col-lg-4">
						<input type="text" class="form-control" id="port" name="port" placeholder="465" required />
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-lg-4 control-label" for="username">Username</label>
					<div class="col-lg-4">
						<input type="text" class="form-control" id="username" name="username" />
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-lg-4 control-label" for="password">Password</label>
					<div class="col-lg-4">
						<input type="password" class="form-control" id="password" name="password" />
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-lg-offset-4 col-lg-4">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="enAuth" name="enAuth" checked />
								Enable SMTP authentication
							</label>
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-lg-offset-4 col-lg-4">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="enSSL" name="enSSL" />
								Enable encryption (SSL)
							</label>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-offset-4 col-lg-4">
						<button type="submit" class="btn btn-block btn-primary" data-loading-text="Sending email..." id="receiveMailButton">Receive an email</button>
					</div>
				</div>
			</form>
		</div>
		<div class="row" style="display: none;" id="console">
			<div class="well well-lg">
				<div class="pull-left">
					<span><strong>Console </strong><span class="badge" id="attempts" title="Attempts">42</span></span>
				</div>
				<div class="pull-right">
					<i class="fa fa-trash-o pointer" title="Clear" id="clearConsole"></i>
					<i class="fa fa-minus-square-o pointer margin-left" title="Hide" id="hideConsole"></i>
				</div>
				<div class="clearfix"></div>
				<hr style="border-style: groove;" />
				<div id="log"></div>
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