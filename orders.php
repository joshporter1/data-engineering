<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <meta name="viewport" content="width=device-width,initial-scale=1">

	<link href="stylesheets/screen.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<link href="stylesheets/print.css" media="print" rel="stylesheet" type="text/css" />
	<!--[if IE]>
		<link href="stylesheets/ie.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<![endif]-->

  <script src="js/libs/modernizr-2.0.6.min.js"></script>
</head>

<body>

  <div id="container">
    <header>
			Orders
    </header>
    <div id="main-orders" role="main">

<?php

	//Connect to MySQL server
	
	//Connect to MySQL server
	$db_host = $_GET["db_host"];
	$db_user = $_GET["db_user"];
	$db_pw = $_GET["db_pw"];
	
	$link = mysql_connect($db_host, $db_user, $db_pw);
	if(!$link){
		die('Could not connect'. mysql_error());
		echo "</br>Incorrect credentials. Please try again.";
	}

	//Get the DB name from form. Create or select DB accordingly
	$db_name = $_GET["db_name"];
	if (mysql_query("CREATE DATABASE ".$db_name,$link)){
	  echo "<p>Database created</p>";
	}
	else if($db_name == ""){
	  echo "<p>Invalid database name.".$db_name."</p>";
	}
	else{
		echo "<p>Viewing Orders from \"".$db_name."\" </p>";
	}
	mysql_select_db($db_name, $link);
	
	//Build query
	$sql = "SELECT Orders.id, Purchasers.purchaser_name, Items.item_description, Items.item_price, Orders.purchase_count, Merchants.merchant_address, Merchants.merchant_name FROM Orders 
	INNER JOIN Items ON Orders.i_id=Items.i_id
	INNER JOIN Purchasers ON Orders.p_id=Purchasers.p_id
	INNER JOIN Merchants ON Orders.m_id=Merchants.m_id";
	//execute query
	$result = mysql_query($sql,$link);
	echo "<table>";
	echo "<tr><th>ID</th><th>Purchaser Name</th><th>Product</th><th>Price</th><th>#</th><th>Merchant Address</th><th>Merchant Name</th></tr>";
	
	while($row = mysql_fetch_array($result)){
		echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td></tr>";
	}
	echo "</table>";
	//close the database
	mysql_close($link);
?>

			<form id="orders" action="result.php" method="POST" enctype="multipart/form-data">

						<input id = "db-name" name="db_name" type="text" value="<?php echo $db_name ?>" style="display:none;">

						<input id = "db-host" name="db_host" type="text" value="<?php echo $db_host ?>" style="display:none;">

						<input id = "db-user" name="db_user" type="text" value="<?php echo $db_user ?>" style="display:none;">

						<input id = "db-pw" name="db_pw" type="text" value="<?php echo $db_pw ?>" style="display:none;">

						<input id="submit" type="submit" value="Back">
			</form>
			</div><!-- End main -->
			
			<footer>
				<div id="about-content">
					<p> Textfile Database Importer - Rapidly developed by <a href="">Joshua Porter</a>. </p>
				</div>
				<a id = "about-button" href="#">
					About
				</a>
		  </footer>
		</div> <!--! end of #container -->


	  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>


	  <!-- scripts concatenated and minified via ant build script-->
	  <script defer src="js/plugins.js"></script>
	  <script defer src="js/script.js"></script>
	  <!-- end scripts-->


	  <script> // Change UA-XXXXX-X to be your site's ID
	    window._gaq = [['_setAccount','UAXXXXXXXX1'],['_trackPageview'],['_trackPageLoadTime']];
	    Modernizr.load({
	      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
	    });
	  </script>


	  <!--[if lt IE 7 ]>
	    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
	    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
	  <![endif]-->
	
	
		<!-- Script for About panel -->
		<script type="text/javascript">
			jQuery(document).ready(function($){
				
				//click listener
				$('#about-button').click(function(event){
					event.preventDefault();
					$('#about-content').slideToggle(500, function(){});
					
				});
			});
			
			</script>

	</body>
</html>