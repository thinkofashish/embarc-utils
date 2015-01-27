<?php
require_once('/var/www/embarc-utils/php/sessions.php');
$session = new SESSIONS();
$session->check();
$session->isModuleAuthorized("5");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <title>Users - Add</title>
    <link rel="stylesheet" href="/embarc-utils/css/normalize.css">
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
	<link href="/embarc-utils/css/custom_style.css" rel="stylesheet">
	<link href="/embarc-utils/css/datepicker.css" rel="stylesheet">
	<link href="/embarc-utils/css/select2.css" rel="stylesheet">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>   
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	<script src="/embarc-utils/js/bootstrap-datepicker.js"></script>
   	<script src="/embarc-utils/js/jquery.validate.min.js"></script>
	<script src="/embarc-utils/js/select2.js"></script>
    <script src="/embarc-utils/js/common.js"></script>
	<script src="/embarc-utils/js/users.js"></script>
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
                    </button>
                <a class="navbar-brand" href="/embarc-utils/dashboard.php">Embarc</a>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse nav-collapse-scrollable">
            	
                    <ul class="nav navbar-nav">                        
                        <li><a href="/embarc-utils/users/users_list.php">List</a></li>
                        <li class="active"><a href="#">Add</a></li>
					</ul>
                    <ul class="nav navbar-nav navbar-right">                      
                        <li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
                    </ul>                
            </div>
    	</div>
    </nav>
    <div class="containt container">
		<div class="alert alert-danger" id="errorMessage-1"><strong>Oh snap!</strong> An error occurred, please try again. </div>
        <div class="alert alert-warning" id="errorMessage-2"><strong>Bummer!</strong> The username you are trying to register is already in use. Please choose a different username. </div>
    	<div class="alert alert-success" id="successMessage-1"><strong>Well done!</strong> user details have been saved successfully. </div>

          <form class="form-horizontal" role="form" id="userAddForm">
		  <div class="row">
			<h3>User Details</h3>
			<hr>
			</div>
			<div class="form-group">
                <label class="col-lg-4 control-label" for="name">Name</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="name" name="name" placeholder="full name" required>
                </div>
            </div>
			
			<div class="form-group">
                <label class="col-lg-4 control-label" for="dob">Date of birth</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control datepicker" style="padding: 6px 12px;" id="dob" name="dob" placeholder="dd/mm/yyyy" required>
                </div>
            </div>
                        
            <div class="form-group">
                <label class="col-lg-4 control-label" for="email">Email</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="email" name="email" placeholder="email" required>
                </div>
            </div>
			
			<div class="form-group">
                <label class="col-lg-4 control-label" for="phone">Phone</label>
                <div class="col-lg-4">
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="phone number">
                </div>
            </div>
			
			<div class="form-group">
                <label class="col-lg-4 control-label" for="username">Username</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="username" name="username" placeholder="username" required>
                </div>
            </div>
			
            <div class="form-group">
                <label class="col-lg-4 control-label" for="password">Password</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="password" name="password" placeholder="password" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-lg-4 control-label" for="modules">Modules</label>
                <div class="col-lg-4">
					<select style="width: 100%;" id="modules" name="modules" multiple="multiple" required></select>
                </div>
            </div>
            <div class="col-lg-offset-4 col-lg-4 margin-bottom">
                <button type="submit" class="btn btn-block btn-primary" id="saveUserButton" data-loading-text="Saving...">Submit</button>
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