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
    <script src="bootstrap/js/bootstrap-modal.js"></script>
    

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
<body>
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
                            <li class="active"><a href="/embarc-utils/attendance/settings_attend.php">Settings</a></li>
                            <li><a href="/embarc-utils/php/main.php?util=login&fx=logout">Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="containt container">
        <!-- Button to trigger modal -->
    <!--<a class="btn btn-primary btn-large" href="#myModal" data-toggle="modal">Launch demo modal</a>-->
    <form method="post" action="/embarc-utils/php/main.php?util=courier&fx=attendanceCalculate" enctype="multipart/form-data">
    	<input type="file" id="emp_data" name="emp_data" />
        <input type="submit" />
    </form>
     <button class="btn btn-info" type="button" onclick="$('#myModal').modal();">Launch modal</button>
    <!-- Modal -->
       <div class="modal hide fade" id="myModal">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Holidays</h3>
    </div>
    <div class="modal-body">
    <p>Show calendar here...</p>
    </div>
    <div class="modal-footer">
    <a href="" class="btn">Close</a>
    <a href="" class="btn btn-primary">Save</a>
    </div>
    </div>
    </div>
</body>
</html>