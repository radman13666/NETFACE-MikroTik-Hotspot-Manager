<?php require "disabler.php";?>
<!DOCTYPE html>
<html>
<head>
<style>
h2{text-align:center;}
ul {
    list-style-type: none;
    margin: 0;
    margin-top: 120px;
    padding: 0;
    width: 250px;
    background-color: #f1f1f1;
    height:auto;
    overflow:auto;
    position:fixed;
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

 /*Change the link color on hover */
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
width:100px

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
require 'routeros_api.class.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = "mgt";
	$servername = "localhost";
  $password = "admin_mgt";
  $dbname = "manager_db";

  $new_username = isset($_POST["new_admin_username"]) ? 
                  $_POST["new_admin_username"] : '';
  $new_password = isset($_POST["new_admin_password"]) ? 
                  $_POST["new_admin_password"] : '';

  $old_username = isset($_POST["old_admin_username"]) ? 
                  $_POST["old_admin_username"] : '';
  $old_password = isset($_POST["old_admin_password"]) ?
                  $_POST["old_admin_password"] : '';

  $new_radaddr = isset($_POST["new_radaddress"]) ? 
                 $_POST["new_radaddress"] : '';
  $new_radsecret = isset($_POST["new_radsecret"]) ? 
                   $_POST["new_radsecret"] : '';
  
  $conn_1 = new mysqli($servername, $username, $password, $dbname);

  if ($conn_1->connect_error) {
    die("Connection failed: " . $conn_1->connect_error);
  }

  $sql_1 = "update login set username=\"$new_username\",".
            " password=\"$new_password\" where id = 1";
  $sql_2 = "select * from login where id = 1";
  $sql_3 = "update radlogin set radaddress=\"$new_radaddr\",".
            " radsecret=\"$new_radsecret\" where id = 1";
  
  #get the old credentials
  $result = $conn_1->query($sql_2);
  $row = $result->fetch_assoc();
  $db_username = $row["username"];
  $db_password = $row["password"];

  #validate the user with the credentials
  if ($old_username === $db_username and $old_password === $db_password) {
    if ($conn_1->query($sql_1) === FALSE) {
      die("Query \"$sql_1\" failed!");
    } else {
      #change the credentials of the routers
      $conn_2 = new mysqli($servername, "radius", "radpass", "radius");
      if ($conn_2->connect_error) {
        die("Connection failed: " . $conn_2->connect_error);
      }
      $sql_4 = "select nasname from nas";
      $result_2 = $conn_2->query($sql_4);
      if ($result_2->num_rows > 0) {
        $API = new RouterosAPI();
        $API->debug = true; # hope this doesn't mess stuff up
        while ($row = $result_2->fetch_assoc()) {
          if ($API->connect($row["nasname"], $old_username, $old_password)) {
            $API->write("/user/set", false);
            $API->write("=.id=*1", false);
            $API->write("=name=$new_username", false);
            $API->write("=password=$new_password");
            $READ = $API->read(false);
          }
        } 
        $msg_1 = "<br><p>
                  <span style=\"color:green;\">
                  Credentials saved</span>
                  </p><br>";
      } else {
        $msg_1 = "<br><p>
                  <span style=\"color:green;\">
                  Credentials saved </span>
                  <span style=\"color:red;\">
                  but no routers added</span>
                  </p><br>";
      }
      $conn_2->close();
    }
    if ($conn_1->query($sql_3) === FALSE) {
      die("Query \"$sql_3\" failed!");
    } else {
      $msg_2 = "<br><p>
      <span style=\"color:green;\">RADIUS settings saved</p><br>";
    }
  }

  $conn_1->close();

}
?>
</p>
<?php require 'menu.php';?>
<h2>GENERAL SETTINGS</h2>
<div class="form">
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" 
      method="post">
<fieldset>
<legend><b>RADIUS<b></legend>
<label>New IP Address:</label>
<input type="text" name="new_radaddress" required="required"><br><br>
<label>New Secret:</label>
<input type="password" name="new_radsecret" required="required"><br><br>
</fieldset>

<fieldset>
<legend><b>ADMIN<b></legend>
<label>New Username:</label>
<input type="text" name="new_admin_username" required="required"><br><br>
<label>New Password:</label>
<input type="password" name="new_admin_password" required="required"><br><br>
</fieldset>

<fieldset>
<legend><b>CONFIRM YOUR IDENTITY<b></legend>
<label>Current Username:</label>
<input type="text" name="old_admin_username" required="required"><br><br>
<label>Current Password:</label>
<input type="password" name="old_admin_password" required="required"><br><br>
<input type="submit" value="SUBMIT"><br><br>
</fieldset>
</form>

<form action="<?php echo htmlspecialchars('upload_Logo.php');?>"
      method="post" enctype="multipart/form-data">
<fieldset>
<legend><b>CUSTOM LOGO<b></legend>
<label>PNG Image (500 KB Max)</label>
<input type="file" name="fileToUpload" id="fileToUpload"><br><br>
<input type="submit" value="UPLOAD"><br><br>
</fieldset>
</form>
<?php if(isset($msg_1)) {echo $msg_1;}
      if(isset($msg_2)) {echo $msg_2;} ?>
</div>

<script>
document.getElementById("script").innerHTML = "";
</script>

</body>

</html>
