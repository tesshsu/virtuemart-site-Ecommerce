<?php
//select here the size of your pop up
$width = 640;
$height = 350;
//you can change this value to "1" or "2" to change the background color and the background-image. "1" is for a pink and orange style. "2" is for a blue and green style.
$color = 1;
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

/***general pop up style***/
#sbox-window{padding:5px !important}
.acymailing_fulldiv form{margin:0px; padding:0px; font-family:"Helvetica Neue",Helvetica,Arial,sans-serif; font-size:12px}
	
/***Style for a list***/	
.acymailing_introtext ul{margin:10px 15px; padding:0px}
.acymailing_introtext ul li{list-style-type:disc;}

/***STYLE 1 - blue and green with dark background-picture***/
<?php if($color == 1){ ?>
/*Intro text area*/
.acymailing_module_form .acymailing_introtext{float:left; background:url("media/com_acymailing/plugins/squeezepage/left_picture_dark.png") no-repeat bottom; width:50%; color:#fff; background-color:#44aaa8; padding:30px !important; font-family:Arial, Helvetica, sans-serif; font-size:14px; height:<?php echo ($height-80);?>px !important}

/*Subscribe form area*/
.acymailing_module_form .acymailing_form{float:left; width:30%; padding:30px !important; background:url("media/com_acymailing/plugins/squeezepage/right_picture_dark.png") no-repeat bottom; background-color:#c0c835; height:<?php echo ($height-80);?>px !important}
<?php } ?>


/***STYLE 2 - pink and orange with light background-picture***/
<?php if($color == 2){ ?>
/*Intro text area*/
.acymailing_module_form .acymailing_introtext{float:left; background:url("media/com_acymailing/plugins/squeezepage/left_picture_light.png") no-repeat bottom; width:50%; color:#fff; background-color:#ed973a; padding:30px !important; font-family:Arial, Helvetica, sans-serif; font-size:14px; height:<?php echo ($height-80);?>px !important}

/*Subscribe form area*/
.acymailing_module_form .acymailing_form{float:left; width:30%; padding:30px !important; background:url("media/com_acymailing/plugins/squeezepage/right_picture_light.png") no-repeat bottom; background-color:#ba5070; height:<?php echo ($height-80);?>px !important}

<?php } ?>


/***Main title in your description***/
.acymailing_module_form h1{color:#fff; font-size:20px; text-transform:uppercase; font-weight:normal; margin-top:0px; margin-bottom:15px}


/***Inputs in your subscribe form***/
.acymailing_form input, .acymailing_form .inputbox{background-color:#fff; opacity:0.6; height:30px; width:180px !important; padding:2px 10px !important; border:none !important; background-image:none !important; margin:2px 0px !important; text-transform:uppercase; color:#444 !important}

.acymailing_form input:hover, .acymailing_form .inputbox:hover{opacity:0.8; border:none !important}

.acymailing_module .acymailing_module_form p{margin:4px 0px !important}


/***buttons in your subscribe form***/
.acysubbuttons input.button, .acysubbuttons .button, .acysubbuttons button.validate, .acymailing_mootoolsbutton a:link, .acymailing_mootoolsbutton a:visited {background-image:none !important; background-color:transparent !important; border:1px solid #fff !important; color:#fff !important; width:auto !important; text-shadow:none !important; float:left !important; margin-top:10px !important; opacity:1 !important; padding:5px 10px !important}

.acysubbuttons input.button:hover, .acysubbuttons .button:hover, .acysubbuttons button.validate:hover, .acymailing_mootoolsbutton a:hover {background-color:#fff !important; -moz-transition: all 0.5s ease-in; -webkit-transition: all 0.5s ease-in; -o-transition: all 0.5s ease-in; transition: all 0.5s ease-in; background-image:none !important; color:#444 !important; opacity:0.8 !important; cursor:pointer}

</style>
</head>
 
 <body>
{module}
</body>
</html>