<?php require "disabler.php";?>
<!DOCTYPE html>
<html>

<head>

<style>

table {width:43%;}

table, th, td {
	border:1px solid grey; border-collapse:collapse; padding:5px;
} 

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
/*background-image:url("wi-fi-img.png");
background-repeat:no-repeat;*/
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
<p>
<?php
//require 'mik_loginpage_upload.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $servername = "localhost";
  $username = "radius";
  $password = "radpass";
  $dbname = "radius";
  
  $addr = $_POST["router_addr"];
  $name = $_POST["router_name"];
  
  $conn_1 = new mysqli($servername, "mgt", "admin_mgt", "manager_db");
  $conn_2 = new mysqli($servername, $username, $password, $dbname);

  if ($conn_1->connect_error or $conn_2->connect_error) {
    die("Connection failed: " . $conn_1->connect_error . " " 
          . $conn_2->connect_error);
  }

  $sql_1 = "select radsecret from radlogin where id = 1";
  $result = $conn_1->query($sql_1);
  $row = $result->fetch_assoc();
  $secret = $row["radsecret"];
  $sql_2 = "insert into nas (nasname, secret, shortname) values " .
           "(\"$addr\", \"$secret\", \"$name\")";

  if ($conn_2->query($sql_2) == FALSE) {
    $msg = "<p><span style=\"color:red\">Error adding router</span></p>";
  } else {
    $msg = "<p><span style=\"color:green\">Router added</span></p>";
  }

  $conn_1->close();
  $conn_2->close();
}
?>
</p>
<?php require 'menu.php'; ?>

<h2>ROUTERS</h2>
<div class="form">
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
      method="post">
<fieldset>
<legend><b>ADD ROUTER<b></legend>
<label>IP Address:</label>
<input type="text" name="router_addr" required="required"><br><br>
<label>Name:</label>
<input type="text" name="router_name" required="required"><br><br>
<input type="submit" value="ADD">
</fieldset>
</form>
</div>
<br>
<div class="form">
<form action="<?php echo htmlspecialchars('remove_router.php');?>"
      method="post">
<fieldset>
<legend><b>REMOVE ROUTER<b></legend>
<label>IP address:</label>
<input type="text" name="router_addr" required="required"><br><br>
<input type="submit" value="REMOVE"> <!--make a dropdown list-->
</fieldset>
</form>
</div>
<?php
function uploadMikLoginPage($router_addr, $login_file) { 
  $trg_file_path = "/hotspot/$login_file";
  $conn_id = ftp_connect($router_addr);
  $conn_1 = new mysqli("localhost", "mgt", "admin_mgt", "manager_db");
  if ($conn_1->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $sql_2 = "select * from login where id = 1";
  $result = $conn_1->query($sql_2);
  $row = $result->fetch_assoc();
  $login_result = ftp_login($conn_id, $row["username"], $row["password"]);
  if (ftp_put($conn_id, $trg_file_path, 
              "/var/www/html/init/" . $login_file, FTP_ASCII)) {
    echo "<p><span style=\"color:green\">
          Successfully uploaded your login page to router</span></p>";
  } else {
    echo "<p><span style=\"color:red\">
          There was a problem while uploading your login page to router
          </span></p>";
  }
  $conn_1->close();
  ftp_close($conn_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $addr = $_POST["router_addr"];
  if(isset($msg)) {echo $msg;}
  uploadMikLoginPage($addr, "login.html");
}
	$servername = "localhost";
  $username = "radius";
  $password = "radpass";
  $dbname = "radius";
  

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql_2 = "select nasname, shortname from nas";
  $result = $conn->query($sql_2);

	echo "<h2>ROUTERS ADDED</h2>";

	echo "<table align='center'>";

	echo "<tr>";
	echo "<th>ROUTER NAME</th>";
	echo "<th>ROUTER ADDRESS</th>";
	echo "</tr>";

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>" . $row["shortname"] . "</td>";
	    echo "<td>" . $row["nasname"] . "</td>";
      echo "</tr>";
    }
  } else {
    echo "<tr>";
    echo "<td> --- </td>";
    echo "<td> --- </td>";
    echo "</tr>";
  }

	echo "</table>";

  $conn->close();

?>

</body>

</html>
