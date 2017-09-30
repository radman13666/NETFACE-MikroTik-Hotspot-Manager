<!DOCTYPE html>
<html>
<head>
<style>
body{
background-image:url("imgback.jpg");
}
fieldset, legend{font-family:Arial;}

h1,h3{
text-align:center;
font-family:oblique;

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

input,label {
float:left;
border-radius:10px;
}
fieldset {
width:350px;
border: 1px solid #dcdcdc;
border-radius: 10px;
padding: 20px;
text-align: right;}
legend {
background-color: #efefef;
border: 1px solid #dcdcdc;
border-radius: 10px;
padding: 10px 20px;
text-align: left;
}

input[type=submit]{
color:white;
text-shadow: 0px 1px 1px #ffffff;
border-bottom: 2px solid #b2b2b2;
background-color: #333;
height:40px;
}
</style>
</head>
<?php session_start(); /* Starts the session */

	
	/* Check Login form submitted */	
	if(isset($_POST['Submit'])){
		/* Define username and associated password array */
//$logins = array('netface' => '123456','username1' => 'password1','username2' => 'password2');
		$username = "mgt";
    $password = "admin_mgt";
    $server = "localhost";
    $db = "manager_db";

    $conn = new mysqli($server, $username, $password, $db);

    if ($conn->connect_error) {
      die("Connection failed: " . $conn->conncet_error);
    }

    $sql_1 = "select * from login where id = 1";
    $result = $conn->query($sql_1);
    $row = $result->fetch_assoc();
    $db_username = $row["username"];
    $db_password = $row["password"];

		/* Check and assign submitted Username and Password to new variable */
		$Username = isset($_POST['Username']) ? $_POST['Username'] : '';
		$Password = isset($_POST['Password']) ? $_POST['Password'] : '';
		
		/* Check Username and Password existence in defined array */		
		if ($Username === $db_username & $Password === $db_password){
			/* Success: Set session variables and redirect to Protected page  */
		    
			$_SESSION['UserData']['Username']=$db_password;
			
			header("location:routers.php");
			
			exit;
		} else {
			/*Unsuccessful attempt: Set error message */
			$msg="<span style='color:red'>Invalid Login Details</span>";
		}
	}
?>

<img src="logo/company_logo.png" alt="Company Logo" width=100 height=100>

<h1>WELCOME TO NETFACE</h1>
<h3>Your configurations partner</h3>
<div class="form">
<form action="" method="post" name="Login_Form">
<fieldset>
<legend><b>LOGIN DETAILS</b></legend>
  <table width="400" border="0" align"center" cellpadding="5" cellspacing="1" class="Table">
    <?php if(isset($msg)){?>
    <tr>
      <td colspan="2" align="center" valign="top"><?php echo $msg;?></td>
    </tr>
    <?php } ?>
   
    <tr>
      <td align="right" valign="top"><b>Username</b></td>
      <td><input name="Username" type="text" class="Input"></td>
    </tr>
    <tr>
      <td align="right"><b>Password</b></td>
      <td><input name="Password" type="password" class="Input"></td>
    </tr>
    <tr>
      <td> </td>
      <td><input name="Submit" type="submit" value="LOGIN" class="Button3"></td>
    </tr>
  </table>
</fieldset>

</form>
</div>
<script type="text/javascript">
  function noBack() {
    window.history.forward()
  }
  noBack();
  window.onload = noBack;
  window.onpageshow = function(evt) { if (evt.persisted) noBack() }
  window.onunload = function() { void (0) }
</script>
<script>
window.location.hash="no-back-button";
window.location.hash="Again-No-back-button";//again because google chrome don't insert first hash into history
window.onhashchange=function(){window.location.hash="no-back-button";}
</script>
</html>
