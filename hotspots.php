<?php require "disabler.php";?>
<!DOCTYPE html>
<html>
<head>
<style>
table {width:43%;}
table, th, td {
	border:1px solid grey; border-collapse:collapse; padding:5px;	}

h2{text-align:center;}
ul {
    list-style-type: none;
    margin: 0;
    margin-top: 120px;
    padding: 0;
    width: 250px;
    background-color: #f1f1f1;
    height:auto;
    position:fixed;
    overflow:auto;
    border:1px solid;
    font-weight:bold;
    text-transform:uppercase;
}

li a {
    display:block;
    color: #000;
    padding: 8px 0 8px 16px;
    text-decoration: none;
    text-align:left;
    border-bottom:1px solid;
}

/* Change the link color on hover */
li a:hover {
    background-color: #555;
    color: white;
}

li a:.active {
    background-color: #4CAF50;
    color: white;
}
li:last-child {
    border-bottom: none;
}
    
*{font-family:Arial;}
label{
width:180px;
clear:left;

padding-right:0px;
}

input,label {
float:left;
border-radius:10px;
}
body {
background-color:#DFE2DB; 
}
input[type=submit]{
color:white;
text-shadow: 0px 1px 1px #ffffff;
border-bottom: 2px solid #b2b2b2;
background-color: #333;
height:50px;
width:100px;
}

fieldset {
border: 1px solid #dcdcdc;
border-radius: 10px;
padding: 20px;
text-align: left;}
legend {
background-color: #efefef;
border: 1px solid #dcdcdc;
border-radius: 10px;
padding: 10px 20px;
text-align: left;
}

div.form
{
display:block;
text-align:center;
}
form
{
display:inline-block;
margin-left:auto;
margin-right:auto;
text-align:left;
}
</style>
</head>

<body>
<p id="script">
<?php
require "configure_hotspot.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $net = $_POST["hs_net"];
  $octs = multiexplode(array(".", "/"), $net);
  if (count($octs) == 5 and $octs[4] >= 22 and $octs[4] <= 29 
      and checkOctet_bool($octs[0]) and checkOctet_bool($octs[1]) 
      and checkOctet_bool($octs[2]) and checkOctet_bool($octs[3])) {
    $servername = "localhost";
    $username = "radius";
    $password = "radpass";
    $dbname = "radius";
  
    $conn_1 = new mysqli($servername, $username, $password, $dbname);
    $conn_2 = new mysqli($servername, "mgt", "admin_mgt", "manager_db");
    if ($conn_1->connect_error or $conn_2->connection_error) {
      die("Connection failed: " . $conn_1->connect_error . " " 
            .$conn_2->connection_error);
    }
  
    $sql_1 = "select nasname from nas";
    $result = $conn_1->query($sql_1);
    
    $sql_2 = "select * from login where id = 1";
    $login = ($conn_2->query($sql_2))->fetch_assoc();

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        setup($row["nasname"], $login["username"], $login["password"]);
      }
    } 
    $conn_1->close(); 
    $conn_2->close();
  } else {
    //$msg = "<p><span style=\"color:red;\">
    //        Invalid subnet mask or network</span></p>";
  }
}
?>
</p>
<?php require 'menu.php'; ?>
<div id="form" class="form">
<h2>HOTSPOTS</h2>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
      method="post">
<fieldset>
<legend><b>SETTINGS FOR ALL HOTSPOTS<b></legend>
<label>SSID:</label>
<input type="text" name="hs_ssid" required="required"><br><br>
<label>Network:</label>
<input type="text" name="hs_net" required="required">
<?php if(isset($msg)) { echo $msg; }?><br><br>
<label>DNS name:</label>
<input type="text" name="hs_dns" required="required"><br><br>
<input type="submit" value="SETUP ALL">
</fieldset>
</form>

<form action="<?php echo htmlspecialchars('setup_one_hotspot.php');?>" 
      method="post">
<fieldset>
<legend><b>SETTINGS FOR ONE HOTSPOT<b></legend>
<label>SSID:</label>
<input type="text" name="hs_ssid" required="required"><br><br>
<label>Network:</label>
<input type="text" name="hs_net" required="required">
<?php if(isset($msg)) { echo $msg; }?>
<br><br>
<label>DNS name:</label>
<input type="text" name="hs_dns" required="required"><br><br>
<label>Router IP Address:</label>
<input type="text" name="router_addr"><br><br> <!--make a dropdown list-->
<input type="submit" value="SETUP">
</fieldset>
</form>

</div>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	echo "<h2>HOTSPOT CREATED WITH THE FOLLOWING SETTINGS</h2>";
	echo "<table align='center'>";

	echo "<tr>";
	echo "<th>HOTSPOT NAME</th>";
	echo "<th>HOTSPOT ADDRESS</th>";
	echo "</tr>";

	echo "<tr>";
	echo "<td>" . $_POST["hs_ssid"] . "</td>";
	echo "<td>" . $_POST["hs_net"] . "</td>";
	echo "</tr>";

	echo "</table>";

	echo '<script src="script.js">';
}

?>
<script>
document.getElementById("script").innerHTML = "";
</script>


</body>

</html>
