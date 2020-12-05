<?php
session_start();
header('Content-text:image/png');
if(isset($_SESSION['captcha'])){
	$text = $_SESSION['captcha'];
	$image = imagecreate(150,50) ;
	imagecolorallocate($image,255,255,255);
	
	$color = imagecolorallocate($image,0,0,30);
	$blue = imagecolorallocate($image,31,58,147);
	$red = imagecolorallocate($image,207, 0, 15);
	$green = imagecolorallocate($image,30, 130, 76) ;
	$ange = rand(2,5) ;
	//$fonts = '../fonts/RAVIE.TTF';
	$font = './../fonts/IranSans/fonts/ttf/IRANSansWeb.ttf';
	// Create
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);

	imagettftext($image,22,$ange,rand(5,11),rand(28,30),$color,$font,$text);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$red);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$green);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$blue);
	imageline($image,rand(0,150),rand(0,50),rand(0,150),rand(0,50),$color);

	imagepng($image);
}
?>