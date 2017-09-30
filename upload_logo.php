<?php require "disabler.php";?>
<!DOCTYPE html>
<html>
<head>
<style>
a:link, a:visited {
    background-color: #f44336;
    color: white;
    padding: 14px 25px;
    text-align: center;	
    text-decoration: none;
    display: inline-block;
}
a:hover, a:active {
    background-color: red;
}
</style>
</head>

<body>
<p></p>
<?php require 'menu.php';?>
<h2>UPLOAD CUSTOM LOGO</h2>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"
      method="post" enctype="multipart/form-data">
Select image to upload (PNG format only):
<input type="file" name="fileToUpload" id="fileToUpload"><br><br>
<input type="submit" value="UPLOAD" name="submit"><br><br>
</form>

<?php
function uploadMikLogo($router_addr, $logo_file) { 
  $trg_file_path = "/hotspot/$logo_file";
  $conn_id = ftp_connect($router_addr);
  $login_result = ftp_login($conn_id, "admin", "");
  if (ftp_put($conn_id, $trg_file_path, "/var/www/html/logo/" . $logo_file,
              FTP_BINARY)) {
    echo "Successfully uploaded your logo to router " . $router_addr;
  } else {
    echo "There was a problem uploading your logo to router " . $router_addr;
  }
  ftp_close($conn_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $target_dir = "logo/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  // Check if image file is a actual image or fake image
  if(isset($_POST["submit"])) {
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if($check !== false) {
          echo "File is an image - " . $check["mime"] . ".";
          $uploadOk = 1;
      } else {
          echo "File is not an image.";
          $uploadOk = 0;
      }
  }
  // Check if file already exists
  if (file_exists($target_file)) {
      echo "Sorry, the logo already exists.";
      $uploadOk = 0;
  }
  // Check file size
  $KiB = 1024;
  if ($_FILES["fileToUpload"]["size"] > (500 * $KiB)) {
      echo "Sorry, your file is too large.";
      $uploadOk = 0;
  }
  // Allow only PNG file format
  if($imageFileType != "png") {
      echo "Sorry, only PNG files are allowed.";
      $uploadOk = 0;
  }
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
      echo "Sorry, your logo was not uploaded.";
  // if everything is ok, try to upload file
  } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],
                              $target_file)) {
          rename($target_file, $target_dir . "company_logo.png");
          $conn = new mysqli("localhost", "radius", "radpass", "radius");
          if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
          }
          $sql_1 = "select nasname from nas";
          $result = $conn->query($sql_1);
          if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
              uploadMikLogo($row["nasname"], "company_logo.png");
            }
          }
          echo "Successfully uploaded your logo to Web UI.";
          $conn->close();
      } else {
          echo "There was an problem while uploading your logo to Web UI.";
      }
  }

}
?> 
</body>
</html> 
