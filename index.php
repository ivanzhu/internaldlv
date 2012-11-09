<?php
//include predefined files
require_once './config/defines.inc.php';
require_once './config/settings.inc.php';



$location = isset($_GET['location']) ? $_GET['location'] : 'Main Page';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>
            Delavente internal WebSite: <?php echo $location?>
        </title>
        <link rel="stylesheet" type="text/css" href="<?php echo _CSS_URL_;?>reset.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo _CSS_URL_;?>index.css" />
        <script type="text/javascript" src="<?php echo _JS_URL_;?>jQuery.js"></script>
        <script type="text/javascript" src="<?php echo _JS_URL_;?>index.js"></script>

    </head>
    <body>
        <div id="base">
            <div id="header">
                Welcome to Delavente Internal WebSite
            </div>
            <div id="left">
                <ul id="menu">
                    <li class="csv">
                        <a id="csv" href="<?php echo _BASE_URL_;?>csv.php?location=csv&action=upload">CSV</a>
                    </li>
					<li class="spider">
						<a id="spider" href=""></a>
					</li>
                </ul>
            </div>

            <div id="right">
                <div id="content">

                </div>
            </div>
            <div id="footer">
                Coded by Ivan Zhu &lt;<a href="mailto:wenhua.ivan@gmail.com">wenhua.ivan@gmail.com</a>&gt;
            </div>
        </div>
    </body>     
</html>

