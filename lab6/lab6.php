<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 6</title>
	</head>
	<body>
		<form method="POST" action="/~tps9tb/cs3380/lab6/lab6.php">
			<select name="query">
				<option value="1" >Query 1</option>
				<option value="2" >Query 2</option>
				<option value="3" >Query 3</option>
				<option value="4" >Query 4</option>
				<option value="5" >Query 5</option>
				<option value="6" >Query 6</option>
				<option value="7" >Query 7</option>
				<option value="8" >Query 8</option>
				<option value="9" >Query 9</option>
				<option value="10" >Query 10</option>
			</select>
			<input type="submit" name="submit" value="Execute" />
		</form>

		<br />
		<hr />
		<br />

		<?php
			include("../../secure/database.php");  // Information for the database 
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD); // Connecting to the database 
			if (!$conn) {
				echo"<p>Failed to connect to DB</p>"; // Error check the connection 
			}

			if(isset($_POST['submit'])) { // Execture when the submit button is pressed 
				print_query();
			}
			else {
				echo "<strong>Select a query from the above list</strong>"; // If the button isn't pressed, do this 
			}

			function print_query() { // This is the function that executes when the submit button is pressed 
				switch($_POST['query']) {
					case "1":
						$query = "SELECT MIN(surface_area) AS min, MAX(surface_area) AS max, AVG(surface_area) AS avg FROM lab6.country;";
	 					break;
	 				case "2":
	 					$query = "SELECT region, SUM(population) AS total_pop, SUM(surface_area) AS total_area, SUM(gnp) AS total_gnp FROM lab6.country GROUP BY region ORDER BY SUM(gnp) desc;";
	 					break;
	 				case "3":
	 					$query = "SELECT government_form, count(country), MAX(indep_year) AS most_recent_indep_year FROM lab6.country WHERE indep_year != 0 GROUP BY government_form ORDER BY count(country) desc, most_recent_indep_year desc";
	 					break;
	 				case "4":
	 					$query = "SELECT lab6.country.name AS name, count(lab6.city.name) as count FROM lab6.country INNER JOIN lab6.city ON (lab6.country.country_code = lab6.city.country_code) GROUP by (lab6.country.name) HAVING count(lab6.city.name) >= 100 ORDER BY count(lab6.city.name) ASC;";
	 					break;
	 				case "5":
	 					$query = "SELECT lab6.country.name AS name, lab6.country.population AS country_population, SUM(lab6.city.population) AS urban_population, ((SUM(lab6.city.population))::numeric / (lab6.country.population)::numeric)*100 AS urban_pct FROM lab6.city INNER JOIN lab6.country ON (lab6.country.country_code = lab6.city.country_code) GROUP BY (lab6.country.country_code) ORDER BY urban_pct ASC;";
	 					break;
	 				case "6":
	 					$query = "SELECT cp.name AS country, lab6.city.name AS largest_city, cp.max_population AS population 
	 										FROM (SELECT lab6.country.country_code AS country_code, lab6.country.name as name, max(lab6.city.population) AS max_population FROM lab6.country, lab6.city
	 											WHERE lab6.country.country_code = lab6.city.country_code
	 											GROUP BY lab6.country.country_code, lab6.country.name)
												AS cp JOIN lab6.city ON cp.country_code = lab6.city.country_code
												WHERE lab6.city.population = cp.max_population ORDER BY max_population DESC"; // uses a sub query to find the max population and join with the country of that city 
	 					break;
	 				case "7":
	 					$query = "SELECT lab6.country.name AS name, count(lab6.city.name) AS count FROM lab6.country INNER JOIN lab6.city ON (lab6.country.country_code = lab6.city.country_code) GROUP BY (lab6.country.name) ORDER BY count(lab6.city.name) DESC, lab6.country.name ASC;";
	 					break;
	 				case "8":
	 					$query = "SELECT lab6.country.name AS name, capitals.name AS capital, cnt_lang.lang_num AS lang_count FROM 
	 										( SELECT ci.name AS name, ci.country_code AS country_code
	 											FROM lab6.city AS ci INNER JOIN lab6.country AS co
	 											ON ci.id=co.capital
	 										) AS capitals
											JOIN 
											( SELECT count(*) AS lang_num, lab6.country.country_code AS country_code 
												FROM lab6.country_language INNER JOIN lab6.country ON 
												(lab6.country.country_code = lab6.country_language.country_code)
												GROUP BY (lab6.country.country_code)
												HAVING count(*) >= 8 AND count(*) <= 12
											) AS cnt_lang
											USING (country_code)
											JOIN lab6.country
											USING (country_code)
											ORDER BY lang_num DESC, capitals.name DESC"; // Uses two subqueries to find the capital of each country and combine with countries who speak 8-12 languages 
	 					break;
	 				case "9":
 						$query = "SELECT lab6.country.name AS country, lab6.city.name AS city, lab6.city.population AS population, sum(lab6.city.population)
 											OVER (PARTITION BY lab6.country.name ORDER BY lab6.city.population DESC) AS running_total 
											FROM lab6.country JOIN lab6.city USING (country_code);";
						break;
					case "10":
						$query = "SELECT lab6.country.name AS name, lab6.country_language.language AS language, rank() OVER (PARTITION BY lab6.country.name ORDER BY lab6.country_language.percentage DESC) 
											AS popularity_rank 
											FROM lab6.country JOIN lab6.country_language USING (country_code);"; // partitions the rank columb over the country names ordered by how much of the country speaks each language
						break;

	 		}


	 		$result = pg_query($query) or die ('Query Failed: ' . pg_last_error()); // If there is no results print the last error 

 				$num_rows = pg_num_rows($result); // finds number of rows and columns 
 				$num_fields = pg_num_fields($result);
					echo"<p>There were <em>".$num_rows."</em> rows returned</p>"; // Prints the number of rows returned 
					echo"<table border = 1>";
					for ($i=0;$i<$num_fields;$i++) { // Prints out all headers for the fields 
						$fieldName = pg_field_name($result, $i);
						echo "\t\t\n<th>$fieldName</th>";  // prints the head out 
					}
					while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
						echo "\t<tr>\n";
						foreach($line as $col_value) {
							echo "\t\t<td>$col_value</td>\n"; // prints each column for the designated row 
						}

						echo"\t</tr>\n";
					}

					echo"</table>\n";

					pg_free_result($result); // frees the results 

 			}
			pg_close($conn); // closes the connection 

		?>
	</body>
</html>