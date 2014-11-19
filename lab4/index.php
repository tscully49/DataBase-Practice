<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>Thomas Scully Lab 4</title>
		<script>
		function clickAction(form, pk, tbl, action)
		{
		  document.forms[form].elements['pk'].value = pk;
		  document.forms[form].elements['action'].value = action;
		  document.forms[form].elements['tbl'].value = tbl;
		  document.getElementById(form).submit();
		}
		</script>
	</head>
	<body>

		<form method="POST" action="/~tps9tb/cs3380/lab4/index.php"> 
		    Search for a :
		    <input type="radio" name="search_by" checked="true" value="country"  />Country 
		    <input type="radio" name="search_by" value="city"  />City
		    <input type="radio" name="search_by" value="language"  />Language <br /><br />
		    That begins with: <input type="text" name="query_string" value="" /> <br /><br />
		    <input type="submit" name="submit" value="Submit" />
		</form>
		<hr />
		Or insert a new city by clicking this <a href="exec.php?action=insert">link</a>

		<?PHP
			include("../../secure/database.php");
	 		$conn=pg_connect(HOST. " ".DBNAME." ".USERNAME." ".PASSWORD); // Connects to the database
	 
    		if(!$conn){
    			echo"<p> Connection Fail</p>";
    		}

    		if(isset($_POST['submit'])) { // If the search is submitted, set the query for the appropriate table
				$input= $_POST['query_string'];
				$input= htmlspecialchars($input."%");
				
				$search = $_POST['search_by']; // Find what table is being searched from 
				
				if($search =='country') { // if the selected button is country, set up the sql statement for that table 
					$result = pg_prepare($conn, "country",'SELECT * FROM lab4.country WHERE name ILIKE $1 ORDER BY name ASC'); // Sets up the query for when the country table is being searched 
					$result = pg_execute($conn, "country", array($input)); // Executes that query and posts the results to $result
				}
				
				if($search =='city') {
					$result = pg_prepare($conn, "city", 'SELECT * FROM lab4.city WHERE name ILIKE $1 ORDER BY name ASC');
					$result = pg_execute($conn, "city", array($input));
				}
				if($search =='language') {
					$result = pg_prepare($conn, "language", 'SELECT * FROM lab4.country_language WHERE language ILIKE $1 ORDER BY language ASC, country_code ASC');
					$result = pg_execute($conn, "language", array($input));
				}
				
				if(!$result) {
					die("pg_last_error".pg_last_error($conn)); // Prints an error statement if the value $result is wrong 
				}

				$num_fields= pg_num_fields($result); 
				$row_num= pg_num_rows($result); // Finds number of rows and fields 
				echo "\n<hr />";
				echo "\n<br />";
				echo "\nThere were $row_num rows returned"; // prints number of rows and sets up the table for all results 
				echo "\n<br /><br />";
				echo "\n<table border='1'>";
				echo"\n\t<tr>";
				echo"\n\t\t<th>Actions</th>";

				for ($i=0;$i<$num_fields;$i++) { // Prints out all headers for the fields 
					$fieldName = pg_field_name($result, $i);
					echo "\t\t\n<th>$fieldName</th>"; 
				}

				while($line=pg_fetch_array($result, null, PGSQL_ASSOC)) {	// Prints out all rows with data and sets the primary key for all the other buttons on the page 
					echo"\n\t<tr>";

					if ($_POST['search_by'] == "city") {
						$pkey = "id";
					}
					else {
						$pkey = "country_code"; // Sets the primary key to be passed to all the buttons and changes the key accordingly 
					}
 
					echo'<td>'; // Create the edit and remove button and pass the given information through the buttons to the different files 
					echo'<form method="POST" action="edit.php">';
					echo'<input type="submit" name="type" value="Edit"/>';
					echo'<input type="hidden" name="pkey" value="'.$line[$pkey].'"/>';
					echo'<input type="hidden" name="tbl" value="'.$search.'"/>';
					echo'<input type="hidden" name="action" value="edit"/>';
					echo'<input type="hidden" name="language" value="'.$line["language"].'"/>';
					echo'</form>';
					echo'<form method="POST" action="exec.php">';
					echo'<input type="submit" name="type" value="Remove"/>';
					echo'<input type="hidden" name="pkey" value="'.$line[$pkey].'"/>';
					echo'<input type="hidden" name="tbl" value="'.$search.'"/>';
					echo'<input type="hidden" name="action" value="remove"/>';
					echo'<input type="hidden" name="language" value="'.$line["language"].'"/>';
					echo'</form>';
					echo'</td>';


					foreach($line as $col) { // Prints out all the info 
						echo"\n\t\t<td>$col</td>";
					}
					echo"\n\t</tr>";
				}
				echo"</table>"; // CLoses the table 
			}
			pg_close($conn);
		?>
	</body>
</html>