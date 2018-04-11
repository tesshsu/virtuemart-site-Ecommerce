<?php

$backgroud = "cccccc"; /* color for backgroud image */
$color = "969696"; /* color for text */

$path = getcwd().DIRECTORY_SEPARATOR.'items'; /*change images directory*/

$dh = opendir($path);

while (false !== ($filename = readdir($dh))) {
    $files[] = $filename;
}

$images = preg_grep('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $files);
//save image level 1
foreach ($images as $key => $value) {
    $image_url = $path . '/' . $value;
    $size = getimagesize($image_url);
    if(is_array($size)){
        create_image($size[0], $size[1], $backgroud, $color,$path,$value);    
    }
}

//save image level 2
$dirs = array();
$dir = dir($path);
while (false !== ($entry = $dir->read())) {
    if ($entry != '.' && $entry != '..') {
       if (is_dir($path . DIRECTORY_SEPARATOR .$entry)) {
            $dirs[] = $entry; 
       }
    }
}

foreach($dirs as $key=>$value){
    $dir=$path.'/'.$value;
    $dh = opendir($dir);
    while (false !== ($filename = readdir($dh))) {
        $files[] = $filename;
    }
    $images = preg_grep('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $files);
    //save image level 2
    foreach ($images as $key => $value) {
        $image_url = $dir . '/' . $value;
        $size = getimagesize($image_url);     
        if(is_array($size)){
            create_image($size[0], $size[1], $backgroud, $color,$dir,$value);    
        }
    }    
}


//Function that has all the magic
function create_image($width, $height, $bg_color, $txt_color, $path, $filename) {
    //Define the text to show
    $text = "$width X $height";

    //Create the image resource 
    $image = ImageCreate($width, $height);

    //We are making two colors one for BackGround and one for ForGround
    $bg_color = ImageColorAllocate($image, base_convert(substr($bg_color, 0, 2), 16, 10), base_convert(substr($bg_color, 2, 2), 16, 10), base_convert(substr($bg_color, 4, 2), 16, 10));

    $txt_color = ImageColorAllocate($image, base_convert(substr($txt_color, 0, 2), 16, 10), base_convert(substr($txt_color, 2, 2), 16, 10), base_convert(substr($txt_color, 4, 2), 16, 10));

    //Fill the background color 
    ImageFill($image, 0, 0, $bg_color);

    //Calculating (Actually astimationg :) ) font size
    $fontsize = ($width > $height) ? ($height / 10) : ($width / 10);

    //Write the text .. with some alignment astimations
    imagettftext($image, $fontsize, 0, ($width / 2) - ($fontsize * 2.75), ($height / 2) + ($fontsize * 0.2), $txt_color, 'Tahoma.ttf', $text);

    //Tell the browser what kind of file is come in 
    header("Content-Type: image/png");

    //Output the newly created image in png format 
    //imagepng($image);
    imagepng($image, $path.'/'.$filename);

    //Free up resources
    ImageDestroy($image);
}

//Ok thank you. Bye
//
?>
