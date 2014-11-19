<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>Thomas Scully Edit Lab 4</title>
	</head>
	<body>
		<?PHP
			/*$event=$_POST['action'];//gets all the post variables from lab4.php
			$pk = $_POST['pk'];
			$table = $_POST['tbl'];*/

			include("../../secure/database.php");//including my database connection
			$conn=pg_connect(HOST. " ".DBNAME." ".USERNAME." ".PASSWORD);//connecting to my database
			if(!$conn){
				echo"<p>Connection Fail</p>";
			}//if no connection it fails

			if ($_POST['tbl'] == 'language') { // Checks to see which table is being used from the POST information sent from index.php
				$table = 'country_language';
			}
			else {
				$table = $_POST['tbl']; // Keeps the current POST information passed from index.php if it isnt from 'language'
			}
			$pkey = $_POST['pkey'];
			$language = null;

			if (isset($_POST['submit'])) { // IF the update button was hit, check which table they are updating from and update the table accordingly 
				if ($table == 'country') {
						$indep_year = htmlspecialchars($_POST['indep_year']); // Run all inputs through htmlspecial chars to avoid a query injection
						$government_form = htmlspecialchars($_POST['government_form']);
						$local_name = htmlspecialchars($_POST['local_name']);
						$population = htmlspecialchars($_POST['population']);

						$query = 'UPDATE lab4.'.$table.' SET population='.$population.', government_form=\''.$government_form.'\', local_name=\''.$local_name.'\', indep_year=\''.$indep_year.'\' WHERE (country_code = \''.$pkey.'\');';
						pg_prepare($conn, "update", $query);
						if (pg_execute($conn, "update", array())) { // Check that the execute statement works 
							echo"Update successful! <br />";
							echo"Return to <a href=\"index.php\">search</a>";
						}
						else { // Run an error message if the execute doesn't work 
							echo"Update Failed...<br />";
							echo "Return to <a href=\"index.php\">search</a>";
						}
						return;
				}

				if ($table == 'city') { // Update the table for "City" with the new values 
						$district = htmlspecialchars($_POST['district']);
						$population = htmlspecialchars($_POST['population']);

						$query = 'UPDATE lab4.'.$table.' SET population='.$population.', district=\''.$district.'\' WHERE id = '.$pkey.';'; // write the query for the 
						pg_prepare($conn, "update", $query);
						if (pg_execute($conn, "update", array())) {
							echo"Update successful! <br />";
							echo"Return to <a href=\"index.php\">search</a>";
						}
						else {
							echo"Update Failed...<br />";
							echo "Return to <a href=\"index.php\">search</a>";
						}
						return;
					}

					if ($table == 'country_language') { // Update the table for "Country_language with new table values "
						$is_official = htmlspecialchars($_POST['is_official']); // Runs inputs through htmlspecialchars to prevent sql injections 
						$percentage = htmlspecialchars($_POST['percentage']);

						$query = 'UPDATE lab4.'.$table.' SET percentage='.$percentage.', is_official=\''.$is_official.'\' WHERE (country_code = \''.$pkey.'\');'; // Sets up the new query accordingly 
						pg_prepare($conn, "update", $query);
						if (pg_execute($conn, "update", array())) { // Error checks the execute to make sure it runs correctly 
							echo"Update successful! <br />";
							echo"Return to <a href=\"index.php\">search</a>";
						}
						else { // Displays an error message if the execute didn't run properly 
							echo"Update Failed...<br />";
							echo "Return to <a href=\"index.php\">search</a>";
						}
						return;
					}
			}

			if ($table == 'country') { // Checks to see which table is being updated 
				$query = 'SELECT * FROM lab4.'.$table.' WHERE (country_code = \''.$pkey.'\');'; // Runs a query to get the whole row being updated from the table 
				$result = pg_prepare($conn, "query", $query);
				$result = pg_execute($conn, "query", array());
				$array = array("local_name", "government_form", "indep_year", "population"); // Creates an array of each field being updated to be checked with later 
			}
			if ($table == 'city') {
				$query = 'SELECT * FROM lab4.'.$table.' WHERE id = '.$pkey.';'; // Runs a query to get the row from lab4.city to be updated 
				$result = pg_prepare($conn, "query", $query); // Prepares the query to be run 
				$result = pg_execute($conn, "query", array()); // Executes the query to grab the row 
				$array = array("population", "district"); // Creates the array of fields to be updated on the row 
			}
			if ($table == 'country_language') { // Runs a query to get the row to be updated 
				$language = $_POST['language'];
				$query = 'SELECT * FROM lab4.'.$table.' WHERE (country_code = \''.$pkey.'\') AND (language=\''.$language.'\');';
				$result = pg_prepare($conn, "query", $query);
				$result = pg_execute($conn, "query", array());
				$array = array("is_official", "percentage"); // Creates the array of field names to be updated 
 			}

			$pkey = $_POST['pkey']; // Sets variables passed from index.php to be used in the update 
			$tbl = $_POST['tbl'];
			$action = $_POST['action'];

			echo"\n\t<form action=\"edit.php\" method=\"POST\">"; // Creates the form for the table and fields to be updated 

			echo"\n\t\t<table border = \"1\">"; // Create the table 
			echo"\n\t\t<input type=\"hidden\" name=\"pkey\" value=\"$pkey\">"; // Created hidden inputs to be passed with the update 
			echo"\n\t\t<input type=\"hidden\" name=\"tbl\" value=\"$tbl\">";
			if ($language) {
				echo"\n\t\t<input type=\"hidden\" name=\"language\" value=\"$language\">";
			}
			echo"\n\t\t<input type=\"hidden\" name=\"action\" value=\"edit\">";

			$i = 0; // Creates a counter 
			while(($line = pg_fetch_array($result, null, PGSQL_ASSOC)) && ($i < pg_num_fields($result))) { // Goes through the row to be updated and prints out all fields 
				foreach ($line as $col_value) {
					$field_names = pg_field_name($result, $i);
					if (in_array($field_names, $array)) { // if a field name matches the array, then make it a field that can be updated and leave a text box that can be changed 
						echo"<tr>";
						echo"<td><strong>$field_names</strong></td>"; // Bold the name 
						echo"<td><input type=\"text\" value=\"$col_value\" name=\"$field_names\"></td>"; // Text box for the input 
						$i++;
					}
					else { // if the field name doesn't match the array, then print it out and make it unchangable 
						echo"<tr>";
						echo"<td>$field_names</td>";
						echo"<td>$col_value</td>";
						$i++;
					}
				}
			}
			echo"</table>"; // close the table 
			echo"<input type=\"submit\" value=\"Save\" name=\"submit\">"; // create the submit button  
			echo"<input type=\"button\" value=\"Cancel\" onclick=\"top.location.href='index.php';\">"; // create the cancel button 
			echo"</form>";
		?>
	</body>
</html>