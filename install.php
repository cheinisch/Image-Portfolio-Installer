<?php

    // Installer für Image Portfolio
    // Läd die aktuelle Version aus dem Netz runter und entpackt diese
    // Es muss nur noch der Adminuser, DB Login, und Sitename angegeben werden
    // Danach löscht sich der installer selbst

    if(isset($_GET['install']))
    {

      $options = [
        'cost' => 11,
      ];

      downloadUnzipGetContents("https://app.image-portfolio.org/downloads/latest.zip");

      // Modify config file
      // Move file
      moveFile();
      // Modify Host, Password, User, Prefix
      $host = $_POST['serverhost'];
      $name = $_POST['servername'];
      $password = $_POST['serverpassword'];
      $user = $_POST['serveruser'];
      $prefix = $_POST['serverprefix'];

      $siteuser = $_POST['username'];
      $sitemail = $_POST['usermail'];
      $sitepassword = password_hash($_POST['userpassword'], PASSWORD_BCRYPT, $options);
      $sitetitle = $_POST['sitetitle'];
      if(isset($_POST['sitetagline']))
      {
      $sitetagline = $_POST['sitetagline'];
      }else{
        $sitetagline = "";
      }
      replace_in_file('ip_config.php', "'DB_HOST', ''", "'DB_HOST', '".$host."'");
      replace_in_file('ip_config.php', "'DB_NAME', ''", "'DB_NAME', '".$name."'");
      replace_in_file('ip_config.php', "'DB_PASSWORD', ''", "'DB_PASSWORD', '".$password."'");
      replace_in_file('ip_config.php', "'DB_USER', ''", "'DB_USER', '".$user."'");
      replace_in_file('ip_config.php', "'DB_PREFIX', ''", "'DB_PREFIX', '".$prefix."'");




      // Create Tables and Config

      replace_in_file('ipdatabase_install.sql', "ipdatabasename", $name);
      if(isset($_POST['serverprefix']))
      {
        replace_in_file('ipdatabase_install.sql', "CREATE TABLE `", "CREATE TABLE `".$prefix);
        replace_in_file('ipdatabase_install.sql', "ALTER TABLE `", "ALTER TABLE `".$prefix);
      }
      importdatabase('ipdatabase_install.sql',$host,$user,$password,$name,$sitetitle,$sitetagline,$siteuser,$sitepassword,$sitemail,$prefix);

      // Clean Server and Reload page

      deletefiles();

    }else{
      loadhtml();
    }

    function downloadUnzipGetContents($url) {
        
        $zipFile = "latest.zip";

        $zip_resource = fopen($zipFile, "w");

        $ch_start = curl_init();
        curl_setopt($ch_start, CURLOPT_URL, $url);
        curl_setopt($ch_start, CURLOPT_FAILONERROR, true);
        curl_setopt($ch_start, CURLOPT_HEADER, 0);
        curl_setopt($ch_start, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch_start, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch_start, CURLOPT_BINARYTRANSFER,true);
        curl_setopt($ch_start, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch_start, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch_start, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt($ch_start, CURLOPT_FILE, $zip_resource);
        $page = curl_exec($ch_start);
        if(!$page)
        {
        echo "Error :- ".curl_error($ch_start);
        }
        curl_close($ch_start);

        $zip = new ZipArchive;
        $extractPath = ".";
        if($zip->open($zipFile) != "true")
        {
        echo "Error :- Unable to open the Zip File";
        } 

        $zip->extractTo($extractPath);
        $zip->close();
    }

    function moveFile()
    {
      rename("demo_ip_config.php", "ip_config.php");
    }

    /**
     * Replaces a string in a file
     *
     * @param string $FilePath
     * @param string $OldText text to be replaced
     * @param string $NewText new text
     * @return array $Result status (success | error) & message (file exist, file permissions)
     */
    function replace_in_file($FilePath, $OldText, $NewText)
    {
        $Result = array('status' => 'error', 'message' => '');
        if(file_exists($FilePath)===TRUE)
        {
            if(is_writeable($FilePath))
            {
                try
                {
                    $FileContent = file_get_contents($FilePath);
                    $FileContent = str_replace($OldText, $NewText, $FileContent);
                    if(file_put_contents($FilePath, $FileContent) > 0)
                    {
                        $Result["status"] = 'success';
                    }
                    else
                    {
                      $Result["message"] = 'Error while writing file';
                    }
                }
                catch(Exception $e)
                {
                    $Result["message"] = 'Error : '.$e;
                }
            }
            else
            {
                $Result["message"] = 'File '.$FilePath.' is not writable !';
            }
        }
        else
        {
            $Result["message"] = 'File '.$FilePath.' does not exist !';
        }
        return $Result;
    }

    function importdatabase($filename, $mysql_host, $mysql_username, $mysql_password, $mysql_database,$sitetitle,$sitetagline,$username,$userpassword,$usermail,$prefix)
    {
      // Connect to MySQL server
      //mysqli_connect($mysql_host, $mysql_username, $mysql_password) or die('Error connecting to MySQL server: ' . mysqli_error());
      // Select database
      $sql = file_get_contents($filename);
      //echo $sql;
      $mysqli = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);

      /* execute multi query */
      $mysqli->multi_query($sql);
      $mysqli->close();
      $tablename = $prefix."config";

      $sql_data = "INSERT INTO `$tablename` (`id`, `admin_user`, `admin_passwd`, `admin_mail`, `site-title`, `site-name`, `site-tagline`, `theme`) VALUES (NULL, '$username', '$userpassword', '$usermail', '$sitetitle', '$sitetitle', '$sitetagline', 'basis');";
      echo $sql_data;
      $mysqli_data = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
      $mysqli_data->query($sql_data);
      echo $mysqli_data->error;
      $mysqli_data->close();
    }

    function deletefiles()
    {
      unlink('latest.zip');  
      unlink('ipdatabase_install.sql');
        unlink('install.php');
        
        header("Location: index.php");
    }

    function loadhtml()
    {

?>

<!doctype html>
<html lang="en">
    <head>
        <title>ImagePortfolio Installer</title>
        <!-- CSS und JS from server -->
        <link href="https://app.image-portfolio.org/data/css/bootstrap.min.css" rel="stylesheet">
        <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

    </head>
    <body class="bg-dark text-light d-flex flex-column h-100">
    
<div class="container">
  <form action="install.php?install=true" method="post">
  <main>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" src="/docs/5.1/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
      <h2>Oneclick Installer</h2>
      <p class="lead">You need only the information below to install Image Portfolio</p>
    </div>
      <div class="row">
      <div class="col-lg-5 col-md-5">
        <h4 class="mb-3">User Data</h4>
            <div class="col-12">
              <label for="username" class="form-label">Username</label>
              <div class="input-group has-validation">
                <span class="input-group-text">@</span>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
              <div class="invalid-feedback">
                  Your username is required.
                </div>
              </div>
            </div>

            <div class="col-12">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="usermail" name="usermail" placeholder="you@example.com" required>
              <div class="invalid-feedback">
                Please enter a valid email address.
              </div>
            </div>

            <div class="col-12">
              <label for="address" class="form-label">Password</label>
              <input type="text" class="form-control" id="userpasswd" name="userpassword" placeholder="password" required>
              <div class="invalid-feedback">
                Please enter a password.
              </div>
            </div>

      </div>
      <div class="col-lg-7 col-md-7">

          <h4 class="mb-3">Server Data</h4>
          <div class="row">
          <div class="col-sm-6 col-md-6">
              <label for="firstName" class="form-label">Site Title</label>
              <input type="text" class="form-control" id="sitetitle" name="sitetitle" placeholder="" value="" required>
              <div class="invalid-feedback">
                Site Title
              </div>
            </div>

            <div class="col-sm-6 col-md-6">
              <label for="lastName" class="form-label">Site Tagline <span class="text-muted">(Optional)</span></label>
              <input type="text" class="form-control" id="sitetagline" name="sitetagline" placeholder="" value="">
            </div>
    </div>
    <div class="row">
            <div class="col-md-6">
              <label for="cc-name" class="form-label">Database Host</label>
              <input type="text" class="form-control" id="serverhost" name="serverhost" placeholder="localhost" required>
              <div class="invalid-feedback">
                Serverhost is required
              </div>
            </div>
            <div class="col-md-6">
              <label for="cc-name" class="form-label">Database Name</label>
              <input type="text" class="form-control" id="servername" name="servername" placeholder="" required>
              <div class="invalid-feedback">
                Databasename is required
              </div>
            </div>
    </div>
    <div class="row">
            <div class="col-md-6">
              <label for="cc-number" class="form-label">Database User</label>
              <input type="text" class="form-control" id="serveruser" name="serveruser" placeholder="" required>
              <div class="invalid-feedback">
                Database user is required
              </div>
            </div>
            <div class="col-md-6">
              <label for="cc-number" class="form-label">Database Password</label>
              <input type="text" class="form-control" id="serverpassword" name="serverpassword" placeholder="" required>
              <div class="invalid-feedback">
                Database password is required
              </div>
            </div>
    </div>
            <div class="col-md-2">
              <label for="cc-cvv" class="form-label">Table Prefix <span class="text-muted">(Optional)</span></label>
              <input type="text" class="form-control" id="serverprefix" name="serverprefix" placeholder="ip_">
              <div class="invalid-feedback">
                Security code required
              </div>
            </div>
    </div>
    </div>
        <hr>
          <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to install</button>
      </div>
    </div>
  </main>
    </form>
  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">&copy; 2021 - Image Portfolio</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="https://image-portfolio.org">Support</a></li>
    </ul>
  </footer>
</div>


    <script src="/docs/5.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

      <script src="form-validation.js"></script>
    </body>
</html>

<?php
    }

?>