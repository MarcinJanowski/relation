<?php
include ('../../../inc/includes.php');
$canedit = Session::haveRight('profile', CREATE);
if (!$canedit)
{
 echo ":( No esta autorizado";
}
else
{
$order="";
$replace="";
$data="";
$header="";
$sql = $_GET['sqlcsv'];
$sql = str_replace($order, $replace, $sql);
$sql = html_entity_decode(stripslashes($sql));

$result = $DB->query($sql);
$filename = "export_".date("Y-m-d_H-i",time());


$fields = $DB->num_fields($result);

for ( $i = 0; $i < $fields; $i++ )
{
    $header .= $DB->field_name($result,$i) . "\t";
}

while( $row = $DB->fetch_row($result) )
{
    $line = '';
    foreach( $row as $value )
    {                                            
        if ( ( !isset( $value ) ) || ( $value == "" ) )
        {
            $value = "\t";
        }
        else
        {
            $value = str_replace( '"' , '\"' , $value );
            $value = '"' . $value . '"' . "\t";
        }
        $line .= $value;
    }
    $data .= trim( $line ) . "\n";
}
$data = str_replace( "\r" , "" , $data );

if ( $data == "" )
{
    $data = "\n(0) Records Found!\n";                        
}

header("Content-type: application/vnd.ms-excel");
header( "Content-disposition: attachment; filename=".$filename.".csv");
header("Pragma: no-cache");
header("Expires: 0");
print "$header\n$data";
exit;
}
?>