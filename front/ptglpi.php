<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
include ('../../../inc/includes.php');
error_reporting(E_ERROR);
$canedit = Session::haveRight('profile', CREATE);
if (!$canedit)
{
 echo ":( No esta autorizado";
}
else
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml11.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb">
<head><title>MySQL Query Form</title>
<meta name="Author" content="k.mcmanus@gre.ac.uk"/>
<style type="text/css">
body { background:#EEEEDD; padding:10px; }
tr { background:#DDEEFF; }
th { background:#CCDDEE; padding:4px; }
td { background:#EEFFEE; padding:4px; }
.idx { background:#CCDDCC; text-align:right }
</style>
</head>
<body>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="frmsql">
<!-- thanks to the good people at Wrox for the ideas -->
<h2>GLPI - Query Form</h2>
<?php
$order   ='\r\n';
$replace = '';

$query = 'SHOW TABLES';
extract($_POST);
if (isset($_POST['query']))
{
	$query = str_replace($order, $replace, $_POST['query']);
}
$query = stripslashes($query);

//$query = htmlentities($query, ENT_NOQUOTES);
?>
<p>
Please input the SQL query to be executed:<br />
<textarea name="query" cols="80" rows="15"><?php echo $query ?></textarea>
</p><p>
<input type="submit" name="run" value="Run SQL Query"/>
<input type="button" name="Export CSV" value="Export CSV.." onclick="Javascript:fenvia();"/>
<input type="button" name="Limpiar" value="Limpiar!" onclick="Javascript:flimpiar();"/>
</p><p>
<?php
if ( isset($run) ) {
/*
   $link = mysql_connect($host, $user, $passwd) or 
      die('Failed to connect to MySQL server. ' . mysql_error() .'<br />');
   mysql_select_db($dbname,$link) or 
      die('Failed to connect to the database. ' . mysql_error());*/
   
   //$query = addslashes($query);
   //$query =str_replace("'","&#34",$query);

   //$result = mysql_query($query,$link);
   $query = str_replace($order, $replace, $_POST['query']);
   $query = html_entity_decode(stripslashes($query));
   $result = $DB->query($query);
   
   if ($DB->error()) {
      echo "<br>".$DB->error();
   }
   else
   {
	   echo 'Results of query: <b>' . $query . "</b></p>\n";
	   if (is_object($result))
	   {
			   if ( $DB->numrows($result) == 0) {
				  echo 'No se encontraron registros';
			   }
				else
				{
					  echo '<table><thead><tr><th class="idx">Row</th>';
					  for ( $i = 0 ; $i < $DB->num_fields($result) ; $i++ ) {		  
						 echo "<th>" . $DB->field_name($result,$i) . "</th>\n";
					  }
					  echo "</tr></thead>\n<tbody>";
					  for ( $i = 0 ; $i < $DB->numrows($result) ; $i++ ) {			  
						 echo '<tr><td class="idx">' . ($i + 1) . '</td>';
						 $row = $DB->fetch_row($result);
						 for ( $j = 0 ; $j < $DB->num_fields($result) ; $j++ ) {
							echo '<td>' . $row[$j] . '</td>';
						 }
						 echo "</tr>\n";
					  }
					  echo '</tbody></table>';				
				}
	   }
	   else
	   {
			  echo '<p>Query executed successfully</p>';   
	   }
   }  
}
}
Html::closeForm();
?>
<hr />
</form>
<form action="ptglpi2csv.php" method="get" name="frmcsv">
<input type="hidden" name="sqlcsv">
<?
Html::closeForm();
?>
<script>
function fenvia()
{
	document.frmcsv.sqlcsv.value = document.frmsql.query.value;
	document.frmcsv.submit();
}

function flimpiar()
{
	document.frmsql.query.value = '';
}
</script>
</body></html>

