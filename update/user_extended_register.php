<?php require_once(dirname(__FILE__). '/../../../config.php');

global $DB;

$sql_query = "UPDATE {talentospilos_user_extended} SET id_ases_user = 8594 WHERE id_moodle_user = 124292";  
echo $DB->execute($sql_query);
echo "<br><br>";
//$sql_query = "DELETE FROM {talentospilos_user_extended} WHERE id = 5540";  
//echo $DB->execute($sql_query);

?>
