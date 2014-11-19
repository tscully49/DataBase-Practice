<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 2</title>
	</head>
	<body>
		<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
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
			<option value="11" >Query 11</option>
			<option value="12" >Query 12</option>
		</select>
		<input type="submit" name="submit" value="Execute" />
		</form>

		<br />
		<hr />
		<br />

		<?php
			/*DEFINE("HOST","host=dbhost-pgsql.cs.missouri.edu");
			DEFINE("DBNAME","dbname=tps9tb");
			DEFINE("USERNAME","user=tps9tb");
			DEFINE("PASSWORD","password=SxsiikVj");*/
			include("../../secure/database.php");  /////////////////////////////////////// ASK TA ABOUT THIS ISSUE /////////////////
			$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD);
			if (!$conn) {
				echo"<p>Failed to connect to DB</p>";
			}

			if(isset($_POST['submit'])) {
				print_query();
			}
			else {
				echo "<strong>Select a query from the above list</strong>";
			}

			function print_query() {
				switch($_POST['query']) {
					case "1":
						$query = "SELECT district, population FROM lab2.city WHERE (name = 'Springfield') ORDER BY population DESC";
						$header = "<th>district</th><th>population</th>";
	 					break;

					case "2":
						$query = "SELECT name, district, population FROM lab2.city WHERE (country_code = 'BRA') ORDER BY name ASC";
						$header="<th>name</th><th>district</th><th>population</th>";
						break;

					case "3":
						$query = "SELECT name, continent, surface_area FROM lab2.country ORDER BY surface_area ASC LIMIT 20";
						$header="<th>name</th><th>continent</th><th>surface_area</th>";
						break;

					case "4":
						$query = "SELECT name, continent, government_form, gnp FROM lab2.country WHERE (gnp > 200000) ORDER BY name ASC";
						$header="<th>name</th><th>continent</th><th>government_form</th><th>gnp</th>";
						break;

					case "5":
						$query = "SELECT name, life_expectancy FROM lab2.country WHERE (life_expectancy IS NOT NULL) ORDER BY life_expectancy DESC OFFSET 10 LIMIT 10";
						$header="<th>name</th><th>life_expectancy</th>";
						break;

					case "6":
						$query = "SELECT name FROM lab2.city WHERE (name ILIKE 'B%s') ORDER BY population DESC";
						$header="<th>name</th>";
						break;

					case "7":
						$query = "SELECT lab2.city.name AS city_name, lab2.country.name AS country_name, lab2.city.population FROM lab2.city FULL OUTER JOIN lab2.country ON lab2.city.country_code = lab2.country.country_code WHERE (lab2.city.population > 6000000) ORDER BY lab2.city.population DESC";
						$header="<th>name</th><th>country</th><th>population</th>";
						break;

					case "8":
						$query = "SELECT lab2.country.name AS country_name, lab2.country_language.language, lab2.country_language.percentage FROM lab2.country FULL OUTER JOIN lab2.country_language ON lab2.country.country_code = lab2.country_language.country_code WHERE (lab2.country.population > 50000000) AND (lab2.country_language.is_official = false) ORDER BY lab2.country_language.percentage DESC";
						$header = "<th>country</th><th>language</th><th>percentage</th>";
						break;

					case "9":
						$query = "SELECT lab2.country.name, lab2.country.indep_year, lab2.country.region FROM lab2.country FULL OUTER JOIN lab2.country_language ON lab2.country.country_code = lab2.country_language.country_code WHERE (lab2.country_language.language = 'English') AND (lab2.country_language.is_official = true) ORDER BY lab2.country.region ASC, lab2.country.name ASC";
						$header = "<th>name</th><th>indep_year</th><th>region</th>";
						break;

					case "10":
						$query = "SELECT lab2.city.name AS capital, lab2.country.name AS country_name, FLOOR(100*CAST(lab2.city.population AS float)/CAST(lab2.country.population AS float)) AS urban FROM lab2.country FULL OUTER JOIN lab2.city ON lab2.country.country_code = lab2.city.country_code WHERE (lab2.country.capital = lab2.city.id) ORDER BY urban DESC";
						$header = "<th>capital_name</th><th>country_name</th><th>urban_pct</th>";
						break;

					case "11":
						$query = "SELECT lab2.country.name, lab2.country_language.language, (CAST(lab2.country_language.percentage AS float)*CAST(lab2.country.population AS float)/100) AS percent_speakers FROM lab2.country FULL OUTER JOIN lab2.country_language ON lab2.country.country_code = lab2.country_language.country_code WHERE (lab2.country_language.is_official = true) ORDER BY percent_speakers DESC";
						$header = "<th>name</th><th>language</th><th>speakers</th>";
						break;
					
					case "12":
						$query = "SELECT name, region, gnp, gnp_old, (CAST(gnp-gnp_old AS float)/CAST(gnp_old AS float)) AS change_gnp FROM lab2.country WHERE (gnp IS NOT NULL) AND (gnp_old IS NOT NULL) ORDER BY change_gnp DESC ";
						$header = "<th>name</th><th>region</th><th>gnp</th><th>gnp_old</th><th>real_change</th>";
						break;
				}	

				$result = pg_query($query) or die ('Query Failed: ' . pg_last_error());

 				$num_rows = pg_num_rows($result);
					echo"<p>There were ".$num_rows." rows returned</p>";
					echo"<table border = 1>";
					echo $header;
					while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
						echo "\t<tr>\n";
						foreach($line as $col_value) {
							echo "\t\t<td>$col_value</td>\n";
						}

						echo"\t</tr>\n";
					}

					echo"</table>\n";

					pg_free_result($result);

 			}
			pg_close($conn);
		?>
	</body>
</html>