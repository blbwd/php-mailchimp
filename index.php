<?php
if ($_REQUEST['hid_mailchimp'] == 1)
{
  $your_name = $_REQUEST['your_name'];
  $your_email = $_REQUEST['your_email'];
  if(!empty($your_email) && !filter_var($your_email, FILTER_VALIDATE_EMAIL) === false)
  {
    // MailChimp API credentials
    $apiKey = 'ee88a732410e9a8fbcbad2e4c093589d-us15';
    $listID = '2ef7913303';

    // MailChimp API URL
    $memberID = md5(strtolower($your_email));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

    // member information
    $json = json_encode([
        'email_address' => $your_email,
        'status'        => 'subscribed',
        'merge_fields'  => [
            'NAME'     => $your_name
        ]
    ]);

    // send a HTTP POST request with curl
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // store the status message based on response code
    if ($httpCode == 200) {
        $msg = 'You have successfully subscribed to our list.';
    }
    else
    {
        switch ($httpCode) {
            case 214:
                $msg = 'You are already subscribed.';
                break;
            default:
                $msg = 'Some problem occurred, please try again.';
                break;
        }
    }

  }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Add subscriber to list using MailChimp API</title>
</head>
<style type="text/css">
.overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  transition: opacity 500ms;
  visibility: hidden;
  opacity: 0;
  z-index: 999999;
}
.overlay:target {
  visibility: visible;
  opacity: 1;
}

.popup {
  margin: 70px auto;
  padding: 20px;
  background: #fff;
  border-radius: 5px;
  width: 30%;
  position: relative;
  transition: all 5s ease-in-out;
}

.popup .close {
  position: absolute;
  top: 20px;
  right: 30px;
  transition: all 200ms;
  font-size: 30px;
  font-weight: bold;
  text-decoration: none;
  color: #333;
}
.popup .close:hover {
  color: #06D85F;
}
.popup .content {
  max-height: 30%;
  overflow: auto;
}

@media screen and (max-width: 700px){
  .popup{
    width: 70%;
  }
}
</style>
<body>
	<h2><a class="button" href="#popup1">Subscribe Our Newsletter</a></h2>

    <div id="popup1" class="overlay">
      <div class="popup">
        <div class="content">
        <a class="close" href="#">&times;</a>
        <?php if ($msg && $msg!=''){echo $msg;} ?>
        <form name="mailchimp" method="post" action="">
        <input type="hidden" name="hid_mailchimp" value="1">
            <p><label>Name: </label><input type="text" name="your_name" /></p>
            <p><label>Email: </label><input type="text" name="your_email" /></p>
            <p><input type="submit" name="submit" value="SUBSCRIBE"/></p>
        </form>
      </div>
      </div>
    </div>
</body>
</html>
