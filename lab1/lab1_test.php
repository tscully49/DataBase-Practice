<html>
<head>
  <title>PHP Lab 1</title>
  <style>
   table {
    border: 2px solid black;
    margin: auto;
    border-radius: 25px;
    padding: 8px;
   }
   .table td:hover {
    background-color: blue;
    color: white;
    font-weight: bolder;
    cursor: pointer;
    font-size: 1000%;
   }
   .table tr:hover {
      background-color: lightblue;
   }
   p {
    margin: auto;
    text-align: center;
   }
  </style>
<head/>
<body>
<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
  <table border="1">
     <tr><td>Number of Rows:</td><td><input type="text" name="rows" /></td></tr>
     <tr><td>Number of Columns:</td><td><select name="columns">
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="4">4</option>
    <option value="8">8</option>
    <option value="16">16</option>

  </select>
</td></tr>
   <tr><td>Operation:</td><td><input type="radio" name="operation" value="multiplication" checked="yes">Multiplication</input><br/>
  <input type="radio" name="operation" value="addition">Addition</input>
  </td></tr>
  </tr><td colspan="2" align="center"><input type="submit" name="submit" value="Generate" /></td></tr>
</table>
</form>



<?php
  if(isset($_POST['submit'])) {

    if (!(is_numeric($_POST['rows']))) {
      echo "<p>Invalid rows and/or column parameters\n</p>";
      return;
    }
    if ($_POST['rows'] < 0) {
      echo "<p>Enter a positive integer for the number of rows.\n</p>";
      return;
    }


    echo "<p>The ".$_POST['rows']." x ".$_POST['columns']." ".$_POST['operation']." table.\n</p>";

    echo "<table border=1 class=table>";

    for ($i=0; $i<=$_POST['columns']; ++$i) {
      echo "<th>$i</th>";
    }

    for ($i=1; $i<$_POST['rows']+1; ++$i) {
      echo "\t\t\t<tr>\n";
      echo "\t\t\t\t<th>$i</th>";
      for ($j=1; $j<=$_POST['columns']; ++$j) {
        if ($_POST['operation'] == "addition") {
          echo "\t\t\t\t<td align = 'center'>".($j + $i)."</td>";
        }
        if ($_POST['operation'] == "multiplication") {
          echo "\t\t\t\t<td align = 'center'>".($j * $i)."</td>";
        }
      }
      echo "\t\t\t</tr>";
    }
    echo"</table>\n";
  }
   // TODO: Implement me
?>
</body>
</html>