<?php
//select here the size of your pop up
$width = 700;
$height = 385;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  xml:lang="en-gb" lang="en-gb" >
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="robots" content="index, follow" />
  <meta name="keywords" content="newsletter,subscribe" />
  <meta name="title" content="Subscribe to our Newsletter!" />
  <meta name="description" content="Subscribe to our Newsletters to not miss anything from us!" />
  <title>Subscribe to our Newsletter!</title>
  <base href="<?php echo ACYMAILING_LIVE ?>" />
 
<style type="text/css">
 
/*general pop up style*/
.acymailing_fulldiv form{margin:0px; padding:0px; font-family:Arial, Helvetica, sans-serif; font-size:13px}
#sbox-window{padding:0px !important} 
    
/*Style for a list*/
.acymailing_introtext ul{margin:10px 15px; padding:0px}
.acymailing_introtext ul li{list-style-type:disc;}

/*Picture in the background*/
.acymailing_fulldiv form {background:url("media/com_acymailing/plugins/squeezepage/under_picture.png") no-repeat top; width:100%; height:100% !important}

/*Intro text area*/
.acymailing_module_form .acymailing_introtext{color:#333 !important; background:url("media/com_acymailing/plugins/squeezepage/white_bg.png") repeat; padding:30px 30px 0px 30px !important; width:240px !important; float:right !important; margin:30px 30px 0px 30px !important}

/*Main title in your description*/
.acymailing_module_form h1{color:#eb951b; font-size:20px; font-weight:normal; margin-top:0px; margin-bottom:10px; text-transform:uppercase}

/*Subscribe form area*/
.acymailing_module_form .acymailing_form{background:url("media/com_acymailing/plugins/squeezepage/white_bg.png") repeat; padding:0px 30px 30px 30px !important; width:240px !important; float:right !important; margin:0px 30px 30px 30px !important}


/*Inputs in your subscribe form*/
.acymailing_form input, .acymailing_form input:hover, .acymailing_form .inputbox, .acymailing_form .inputbox:hover{background-color:#fff; height:35px; width:200px !important; padding:0px 10px !important; border:none !important; background-image:none !important; margin:0px !important; color:#666 !important; z-index:600 !important}


/*buttons in your subscribe form*/
.acysubbuttons input.button, .acysubbuttons .button, .acysubbuttons button.validate, .acymailing_mootoolsbutton a:link, .acymailing_mootoolsbutton a:visited {background-image:none !important; background-color:#d18c29 !important; border:none !important; color:#fff !important; width:auto !important; text-shadow:none !important; float:left !important; opacity:1 !important; padding:5px 15px !important; margin:0px 10px 0px 0px !important; font-weight:bold; font-size:11px}

.acysubbuttons input.button:hover, .acysubbuttons .button:hover, .acysubbuttons button.validate:hover, .acymailing_mootoolsbutton a:hover {background-color:#333 !important; border:none !important; color:#fff !important; -moz-transition: all 0.5s ease-in; -webkit-transition: all 0.5s ease-in; -o-transition: all 0.5s ease-in; transition: all 0.5s ease-in; background-image:none !important; cursor:pointer;}

</style>
</head>
 
 <body>
{module}
</body>
</html>