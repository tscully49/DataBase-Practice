<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>Thomas Scully Exec Lab 4</title>
	</head>
	<body>
		<?PHP
			$event=$_POST['action'];//gets all the post variables from lab4.php
			$pk = $_POST['pk'];
			$table = $_POST['tbl'];

			include("../../secure/database.php");//including my database connection
			$conn=pg_connect(HOST. " ".DBNAME." ".USERNAME." ".PASSWORD) or die ('Connection failed'.pg_last_error($conn));//connecting to my database

//////////////////////////////////////////////////////// PHP STATEMENTS ////////////////////////////////////////////////////////////////////////////////////
			if (isset($_GET['action'])) {
		?>
				<form method="POST" action="exec.php"> <!-- Creates the form for the addition form -->
				<input type="hidden" name="action" value="save_insert" /> 

				<?PHP
					$result = pg_prepare($conn, "country", "SELECT name, country_code FROM lab4.country ORDER BY name ASC")or die ('prepared failed' . pg_last_error($conn));//Creates the query to find all countries 
					$result = pg_execute($conn, "country", array()) or die ('failed' . pg_last_error($conn)); // Executes the query for finding all countries
					$row_num = pg_num_rows($result); //number of rows that are in the database
					if(!$result) {die ('No Query was found');} // Error checks to make sure there is a valid result 
				?>

				Enter data for the city to be added: <br /> 
				<table border="1">
				<tr><td>Name</td><td><input type="text" name="new_name" /></td></tr> <!-- Type in information such as the city, country code, etc for the new city -->
				<tr><td>Country Code</td><td>
				<select name="new_country_code">
					<?PHP // Creates a drop down bar of all the countries to add a country name to the city that is being added 
						while($line=pg_fetch_array($result, null, PGSQL_ASSOC)) {	
							echo"\n\t<option value = \"".$line['country_code']."\">".$line['name']."</option>";
						}
					?>
				</select></td></tr>
				<tr><td>District</td><td><input type="text" name="new_district" /></td></tr> <!-- Provides inputs for the different values of the new city -->
				<tr><td>Population</td><td><input type="text" name="new_population" /></td></tr>
				</table>
				<input type="submit" name = "save" value= "save" />
				<input type="button" value="Cancel" onclick="top.location.href='index.php';" />
				</form>
			<?PHP
		}

///////////////////////////////////////////////////////////////// Iff add button is pressed /////////////////////////////////////////////////////////////////////

		else if (isset($_POST['save'])) {  // Executes once the submit button for the added city is pressed 
			$new_name=htmlspecialchars($_POST['new_name']);
			$new_country_code=htmlspecialchars($_POST['new_country_code']);
			$new_district=htmlspecialchars($_POST['new_district']);
			$new_population=htmlspecialchars($_POST['new_population']); // Runs all inputs through htmlspecialchars to prevent a sql injection 


			if(!is_numeric($new_name)&&!is_numeric($new_country_code)&&is_numeric($new_population)&&!is_numeric($new_distric)&&$new_population <= 2000000000) { // Error checks a bit for the inputed values 
				$query = 'INSERT INTO lab4.city (name, country_code, district, population) VALUES (\''.$new_name.'\', \''.$new_country_code.'\', \''.$new_district.'\', '.$new_population.');';
				pg_prepare($conn, "insert", $query); // Prepares the query for the addition of the city 

				if (pg_execute($conn, "insert", array())) { // Executes the query and prints an error message if it doesnt work.  Also provides a link back to the search page 
				echo "Insert successful <br/>";
				echo "Return to <a href=\"index.php\">search</a>";
				}
				else{ // Prints an error message if the query doesn't work 
					echo "Insert unsuccessful <br/>";
					echo "Return to <a href=\"index.php\">search</a>";
				}
			}
			else if (!is_numeric($new_population) || $new_population > 2000000000) { // If the population is incorrect, it displays an error message and a link back to the search page 
				echo "Invalid Population input<br />";
				echo "Return to <a href=\"index.php\">search</a><br />";
				echo "Or input again <a href=\"exec.php?action=insert\">input</a>";
			}
			else { // Prints error message 
				echo "Problem with Name, Country, or District <br/>";
				echo "Return to <a href=\"index.php\">search</a>";
				echo "Or input again <a href=\"exec.php?action=insert\">input</a>"; // Provides a link back to the search page as well as back to the input page 
			}
		}

///////////////////////////////////////////////// REMOVE FUNCTION /////////////////////////////////////
		else if (isset($_POST['action'])=='remove') { // If the remove button is pressed from the index.php page, then execute this statement 
			$table = $_POST['tbl'];
			$pkey = $_POST['pkey']; // Sets values passed from the index.php page 

			if ($table == 'country') { // Changes the query depending on from which table the value is being deleted from 
				$query = 'DELETE FROM lab4.'.$table.' WHERE (country_code = \''.$pkey.'\');';
			}
			else if ($table == 'language') { // Sets two more values if the language table is being used 
				$table = 'country_language';
				$language = $_POST['language'];
				$query = 'DELETE FROM lab4.'.$table.' WHERE (country_code = \''.$pkey.'\') AND (language=\''.$language.'\');';
			}
			else {
				$query = 'DELETE FROM lab4.'.$table.' WHERE id = '.$pkey.';';
			}

			pg_prepare($conn, "delete", $query); // Prepares whatever query is active based on the passed $table value 

			if(pg_execute($conn, "delete", array())) { // Executes the query and provides a message if it worked or didn't work 
				echo"Delete Successfull!<br />";
				echo"Return to <a href=\"index.php\">search</a>";
			}
			else { // Provides an error message if the execute didn't run correctly 
				echo"Delete failed<br />";
				echo"Return to <a href=\"index.php\">search</a>";
			}
		}

		else { // If no function is run, prints an error message and the post 
			print_r($_POST);
			echo"Action not found";
		}

		?>
	</body>
</html>