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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>

  <script src="js/libs/modernizr-2.0.6.min.js"></script>
</head>

<body>

  <div id="container">
    
    <div id="main" role="main">
		<header>
			Creating the database...
    </header>
<?php
	error_reporting(0);
	//Connect to MySQL server
	$db_host = $_POST["db_host"];
	$db_user = $_POST["db_user"];
	$db_pw = $_POST["db_pw"];
	
	$link = mysql_connect($db_host, $db_user, $db_pw);
	if(!$link){
		die('Could not connect'. mysql_error());
		echo "</br>Incorrect credentials. Please try again.";
	}

	//Get the DB name from form. Create or select DB accordingly
	$db_name = $_POST["db_name"];
	if (mysql_query("CREATE DATABASE ".$db_name,$link)){
	  echo "<p>Database created</p>";
	}
	//check for empty field
	else if($db_name == ""){
	  echo "<p>Invalid database name.</p>";
	}
	//otherwise, drop the db and create a new one with the selected name
	else{
		if(strpos($_SERVER['HTTP_REFERER'], "orders") >0){
			echo "<p>Using database ".$db_name."</p>";
		}
		else{
			mysql_query("DROP DATABASE ".$db_name,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
			mysql_query("CREATE DATABASE ".$db_name,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
			echo "<p>Database created</p>";
		}
	}
	mysql_select_db($db_name, $link);
	
	
	//get file from upload and move to directory
	$file = $HTTP_POST_FILES['dbfile']['name'];
	
	if(copy($HTTP_POST_FILES['dbfile']['tmp_name'], "upload/".$file)){
		//Open the file
		$fo = fopen("upload/".$file, 'r');
		
		//Create the table from the first line of headings
		//get the first line
		$line = fgets($fo);
		
		//delimit on tabs
		$flinearr = explode("\t" ,$line);
		
		//replace spaces with underscores
		for($i = 0; $i < count($flinearr); $i++){
			$flinearr[$i] = str_replace(" ", "_", $flinearr[$i]);
		}
		
		
		
		//build queries for creating the tables
		//Purchasers table
		$sql = "CREATE TABLE Purchasers
		(
		p_id int NOT NULL AUTO_INCREMENT, 
		PRIMARY KEY(p_id),
		$flinearr[0] varchar(30)
		)";
		//Execute query
		mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
		
		//Items table
		$sql = "CREATE TABLE Items
		(
		i_id int NOT NULL AUTO_INCREMENT, 
		PRIMARY KEY(i_id),
		$flinearr[1] varchar(30),
		$flinearr[2] decimal(5, 2)
		)";
		//Execute query
		mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
		
		//Merchants table
		$sql = "CREATE TABLE Merchants
		(
		m_id int NOT NULL AUTO_INCREMENT, 
		PRIMARY KEY(m_id),
		$flinearr[5] varchar(30),
		$flinearr[4] varchar(30)
		)";
		//Execute query
		mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
		
		
		
		//Orders table
		$sql = "CREATE TABLE Orders
		(
		id int NOT NULL AUTO_INCREMENT, 
		PRIMARY KEY(id),
		p_id int,
		i_id int,
		m_id int,
		purchase_count int
		)";
		//Execute query
		mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
		
		//iterate through the lines and add entries accordingly until end of file
		while(($line = fgets($fo)) != false ){
			//delimit on tabs
			$linearr = explode("\t" ,$line);
			
			//check for special characters and slash them out
			for($i = 0; $i < count($linearr); $i++){
				$linearr[$i] = addslashes($linearr[$i]);
			}
			
			//build insert queries
			//insert into Purchasers
			$sql = "INSERT INTO Purchasers (purchaser_name)
			VALUES ('$linearr[0]')";
			//check for empty value
			if($linearr[0] != ""){
				if(!mysql_num_rows(mysql_query("SELECT $flinearr[0] FROM Purchasers WHERE $flinearr[0] = '$linearr[0]'"))){
					//execute query
					mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
				}
			}
			
			//insert into Items
			$sql = "INSERT INTO Items (item_description, item_price)
			VALUES ('$linearr[1]', '$linearr[2]')";
			//check for empty value
			if($linearr[0] != ""){
				if(!mysql_num_rows(mysql_query("SELECT $flinearr[1] FROM Items WHERE $flinearr[1] = '$linearr[1]'"))){
					//execute query
					mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
				}
			}
			
			//insert into Merchants
			$sql = "INSERT INTO Merchants (merchant_name, merchant_address)
			VALUES ('$linearr[5]', '$linearr[4]')";
			//check for empty value
			if($linearr[0] != ""){
				if(!mysql_num_rows(mysql_query("SELECT $flinearr[4] FROM Merchants WHERE $flinearr[4] = '$linearr[4]'")) && !mysql_num_rows(mysql_query("SELECT $flinearr[5] FROM Merchants WHERE $flinearr[5] = '$linearr[5]'"))){
					//execute query
					mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
				}
			}
			
			//Insert into Orders
			$sql = "INSERT INTO Orders (p_id, i_id, m_id, purchase_count)
			VALUES ((SELECT p_id FROM Purchasers WHERE purchaser_name = '$linearr[0]'), 
			(SELECT i_id FROM Items WHERE item_description = '$linearr[1]'), 
			(SELECT m_id FROM Merchants WHERE merchant_name = '$linearr[5]'), 
			$linearr[3]
			)";
			//check for empty value
			if($linearr[0] != ""){
				//execute query
				mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
			}
		}//end while
		
		//close the file
		fclose($fo);
	}//end if for file copied
	
	else{
		if(strpos($_SERVER['HTTP_REFERER'], "orders") <=0){
			echo "<p>File upload failed. Please try again.</p>";
		}
	}
	
	$sql = "SELECT SUM(Items.item_price*Orders.purchase_count) FROM Orders INNER JOIN Items ON Orders.i_id=Items.i_id;";
	$result = mysql_query($sql,$link) or die('Query failed: ' . mysql_error() . "<br />\n$sql");
	$row = mysql_fetch_array($result);
	echo "Gross Income: ".$row[0];
	
	//close the database
	mysql_close($link);
?>
<!-- Hidden form to pass DB variables to the view orders page. -->
<form id="orders" action="orders.php" method="GET" enctype="multipart/form-data">
	
			<input id = "db-name" name="db_name" type="text" value="<?php echo $db_name ?>" style="display:none;">
		
			<input id = "db-host" name="db_host" type="text" value="<?php echo $db_host ?>" style="display:none;">
		
			<input id = "db-user" name="db_user" type="text" value="<?php echo $db_user ?>" style="display:none;">
		
			<input id = "db-pw" name="db_pw" type="text" value="<?php echo $db_pw ?>" style="display:none;">
		
			<input id="submit" type="submit" value="View the Orders">
</form>
			
			
			</div><!-- End main -->
		  <footer>
				<div id="about-content">
				<p> Textfile Database Importer </p> <p> Rapidly developed by</p><p> <a href="http://joshport.com" target="_blank">Joshua Porter</a> </p>
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