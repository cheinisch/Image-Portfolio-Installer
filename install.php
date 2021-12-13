<?php

    // Installer für Image Portfolio
    // Läd die aktuelle Version aus dem Netz runter und entpackt diese
    // Es muss nur noch der Adminuser, DB Login, und Sitename angegeben werden
    // Danach löscht sich der installer selbst

    if(isset($_GET['install']))
    {
      downloadUnzipGetContents("https://app.image-portfolio.org/downloads/latest.zip");
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
    <body class="bg-light d-flex flex-column h-100">
    
<div class="container">
  <form>
  <main>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4" src="/docs/5.1/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
      <h2>Oneclick Installer</h2>
      <p class="lead">Below is an example form built entirely with Bootstrap’s form controls. Each required form group has a validation state that can be triggered by attempting to submit the form without completing it.</p>
    </div>
      <div class="row">
      <div class="col-lg-5 col-md-5">
        <h4 class="mb-3">User Data</h4>
            <div class="col-12">
              <label for="username" class="form-label">Username</label>
              <div class="input-group has-validation">
                <span class="input-group-text">@</span>
                <input type="text" class="form-control" id="username" placeholder="Username" required>
              <div class="invalid-feedback">
                  Your username is required.
                </div>
              </div>
            </div>

            <div class="col-12">
              <label for="email" class="form-label">Email <span class="text-muted">(Optional)</span></label>
              <input type="email" class="form-control" id="email" placeholder="you@example.com">
              <div class="invalid-feedback">
                Please enter a valid email address for shipping updates.
              </div>
            </div>

            <div class="col-12">
              <label for="address" class="form-label">Password</label>
              <input type="text" class="form-control" id="address" placeholder="1234 Main St" required>
              <div class="invalid-feedback">
                Please enter your shipping address.
              </div>
            </div>

      </div>
      <div class="col-lg-7 col-md-7">

          <h4 class="mb-3">Server Data</h4>
          <div class="row">
          <div class="col-sm-6 col-md-6">
              <label for="firstName" class="form-label">Site Title</label>
              <input type="text" class="form-control" id="firstName" placeholder="" value="" required>
              <div class="invalid-feedback">
                Valid first name is required.
              </div>
            </div>

            <div class="col-sm-6 col-md-6">
              <label for="lastName" class="form-label">Site Tagline</label>
              <input type="text" class="form-control" id="lastName" placeholder="" value="" required>
              <div class="invalid-feedback">
                Valid last name is required.
              </div>
            </div>
    </div>
    <div class="row">
            <div class="col-md-6">
              <label for="cc-name" class="form-label">Database Host</label>
              <input type="text" class="form-control" id="cc-name" placeholder="localhost" required>
              <small class="text-muted">Full name as displayed on card</small>
              <div class="invalid-feedback">
                Name on card is required
              </div>
            </div>
            <div class="col-md-6">
              <label for="cc-name" class="form-label">Database Name</label>
              <input type="text" class="form-control" id="cc-name" placeholder="" required>
              <small class="text-muted">Full name as displayed on card</small>
              <div class="invalid-feedback">
                Name on card is required
              </div>
            </div>
    </div>
    <div class="row">
            <div class="col-md-6">
              <label for="cc-number" class="form-label">Database User</label>
              <input type="text" class="form-control" id="cc-number" placeholder="" required>
              <div class="invalid-feedback">
                Credit card number is required
              </div>
            </div>
            <div class="col-md-6">
              <label for="cc-number" class="form-label">Database Password</label>
              <input type="text" class="form-control" id="cc-number" placeholder="" required>
              <div class="invalid-feedback">
                Credit card number is required
              </div>
            </div>
    </div>
            <div class="col-md-2">
              <label for="cc-cvv" class="form-label">Table Prefix</label>
              <input type="text" class="form-control" id="cc-cvv" placeholder="ip_" required>
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
    <p class="mb-1">&copy; 2017–2021 Company Name</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="#">Privacy</a></li>
      <li class="list-inline-item"><a href="#">Terms</a></li>
      <li class="list-inline-item"><a href="#">Support</a></li>
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