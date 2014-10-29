<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>Thomas Scully Lab 8</title>
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

		<form method="POST" action="/~tps9tb/cs3380/lab8/index.php"> 
		    Register Page! <br />
		    First Name: <input type="text" name="first_name" value="" /><br />
		    Last Name: <input type="text" name="last_name" value="" /><br />
		    Password: <input type="text" name="password" value="" /><br />
		    Re-Enter Password: <input type="text" name="password_2" value="" /><br />
		    <!--<input type="button" name="Register" value="Register" />
		   	<a href="register.php"><input type="button" name="Login" value="Login" /></a>
		    <br />
		    <input type="radio" name="search_by" checked="true" value="country"  />Country 
		    <input type="radio" name="search_by" value="city"  />City
		    <input type="radio" name="search_by" value="language"  />Language <br /><br />
		    That begins with: <input type="text" name="query_string" value="" /> <br /><br />
		    <input type="submit" name="submit" value="Submit" />-->
		</form>
	</body>
</html>