<?php
//select here the size of your pop up
$width = 640;
$height = 390;
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


/*Picture in top*/
.acymailing_fulldiv form{background:url("media/com_acymailing/plugins/squeezepage/top_picture.png") no-repeat top; width:100%; background-color:#fff; height:<?php echo ($height-300);?>px !important; padding-top:250px !important}

/*Intro text area*/
.acymailing_module_form .acymailing_introtext{color:#fff !important; background-color:#065988 !important; padding:10px 30px !important; }

/*Main title in your description*/
.acymailing_module_form h1{color:#fff; font-size:18px; font-weight:bold; margin-top:0px; margin-bottom:5px}

/*Subscribe form area*/
.acymailing_module_form .acymailing_form{float:left; width:100%; padding:0px 0px 10px 0px !important; background-color:#065988;}
.acymailing_module_form p{margin:10px 30px}


/*Inputs in your subscribe form*/
.acymailing_form input, .acymailing_form .inputbox{background-color:#000; opacity:0.4; height:35px; width:200px !important; padding:2px 10px !important; border:none !important; background-image:none !important; color:#fff !important; float:left; margin:0px 8px 8px 0px !important}

.acymailing_form input:hover, .acymailing_form .inputbox:hover{opacity:0.5; border:none !important}


/*buttons in your subscribe form*/
.acysubbuttons input.button, .acysubbuttons .button, .acysubbuttons button.validate, .acymailing_mootoolsbutton a:link, .acymailing_mootoolsbutton a:visited {background-image:none !important; background-color:#5fa7db !important; border:none !important; color:#fff !important; width:auto !important; text-shadow:none !important; float:left !important; margin-top:10px !important; opacity:1 !important; padding:5px 15px !important; margin:0px 8px 8px 0px !important; font-weight:bold; font-size:11px}

.acysubbuttons input.button:hover, .acysubbuttons .button:hover, .acysubbuttons button.validate:hover, .acymailing_mootoolsbutton a:hover {background-color:#fff !important; -moz-transition: all 0.5s ease-in; -webkit-transition: all 0.5s ease-in; -o-transition: all 0.5s ease-in; transition: all 0.5s ease-in; background-image:none !important; color:#5fa7db !important; cursor:pointer; border:none !important;}

</style>
</head>
 
 <body>
{module}
</body>
</html>