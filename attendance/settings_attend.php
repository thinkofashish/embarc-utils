<?php
/*require_once('php/sessions.php');
$session = new SESSIONS();
$session->check();*/
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8" />
    <title></title>
    <link href="css/normalize.css" rel="stylesheet">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="bootstrap/js/bootstrap-alert.js"></script>
    <script src="js/common.js"></script>
	<script src="js/dhl_settings.js"></script>

    <style type="text/css">
        *::-moz-selection {
            background: none repeat scroll 0 0 #E9AC44;
            color: #FFFFFF;
            text-shadow: none;
        }

        body {
        }

        .header_wrap {
            height: auto;
            background-color: #1e1e1e;
            width: 100%;
            margin-bottom: 20px;
        }

        .header {
            height: 60px;
            margin: 0 auto;
            padding-top: 10px;
        }

        .containt {
            overflow: hidden;
            margin: 0 auto;
            width: 800px;
            padding-top: 100px;
        }

        .align_center {
            text-align: center;
        }

        .links {
            padding-top: 10px;
        }

        @media (max-width: 480px) {
            .containt {
                overflow: hidden;
                margin: 0 auto;
                width: auto;
                padding-top: 0px;
            }
        }
    </style>
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="../assets/js/html5shiv.js"></script>
    <![endif]-->

</head>
<body onload="init();">
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner links">
            <div class="container">
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="brand" href="index.html">
                    <img src="image/logo.png" />

                </a>
                <div class="nav-collapse collapse links">
                    <div class="pull-right">
                        <ul class="nav">
                            <li><a href="/embarc-utils/dashboard.php">Dashboard</a></li>
                            <li class="active"><a href="#">Settings</a></li>
                            <li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="containt container">
		<div id="messages"></div>
        <form class="form-horizontal" id="settingsForm">
            <div class="control-group">
                <label class="control-label" for="l_in_t">Late in Time</label>
                <div class="controls">
                        <input type="text" class="span2" id="l_in_t" name="l_in_t" placeholder="Late in Time">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="l_o_t">Late out Time</label>
                <div class="controls">
                        <input type="text" class="span2" id="l_o_t" name="l_o_t" placeholder="Late out Time">
                </div>
            </div>
            
			<div class="controls">
                <button type="button" class="btn btn-success" onclick="saveSettings();">Save</button>
                <button type="button" class="btn" onclick="gotoPage('/embarc-utils/courier/courier_dhl.php');">Cancel</button>
            </div>
        </form>
    </div>
</body>
</html>
