<?php //require "disabler.php"?>;
<!DOCTYPE html>
<html>
<head>
<style>
table {width:43%;}
table, th, td {
	border:1px solid grey; border-collapse:collapse; padding:5px;	} </style>
</head>
<!--<head>
<style>
a:link, a:visited {
	background-color: #f44336;
	color:white;
	padding:14px 25px;
	text-align:center;
	text-decoration:none;
	display:inline-block;
}
a:hover, a:active {
	background-color:red;
}
</style>
</head>-->

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
    $conn = new mysqli("localhost", "mgt", "admin_mgt", "manager_db");
    if ($conn->connection_error) {
      die("Connection failed: " . $conn->connection_error);
    }
    $result = $conn->query("select * from login where id = 1");
    $row = $result->fetch_assoc();
    setup($_POST["router_addr"], $row["username"], $row["password"]);
    $conn->close();
  } else {
    $msg = "<p><span style=\"color:red\">
            Invalid subnet mask or network</span></p>";
  }
}
?>
</p>

<?php //require 'menu.php'; ?>

<!--<div id="form">
<form action="<?php //echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
      method="post">

<h2>SETUP HOTSPOT</h2>

<fieldset>
<legend><b>HOTSPOT SETTINGS<b></legend>
SSID:
<input type="text" name="hs_ssid"><br><br>
Network:
<input type="text" name="hs_net">
<br><br>
DNS name:
<input type="text" name="hs_dns"><br><br>
</fieldset>

<br><br>
<fieldset>
<legend><b>ROUTER<b></legend>
IP Address:
<input type="text" name="router_addr"><br><br>
</fieldset>

<br>
<input type="submit" value="SETUP">
</form>
</div>-->
<?php if(isset($msg)) { echo $msg; }?>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST'and !isset($msg)) {
	echo "<h2>HOTSPOT CREATED WITH THE FOLLOWING SETTINGS</h2>";
	echo "<table align='center'>";

	echo "<tr>";
	echo "<th>HOTSPOT NAME</th>";
	echo "<th>HOTSPOT ADDRESS</th>";
	echo "</tr>";

	echo "<tr>";
	echo "<td>" . $_POST["hs_ssid"] . "</td>";
	echo "<td>" . $_POST["hs_net"]. "</td>";
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
