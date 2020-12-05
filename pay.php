<?php
/**
 * Created by PhpStorm.
 * User: Mohammad
 * Date: 5/11/2017
 * Time: 12:48 PM
 */

require_once 'config.inc.php';
require_once 'functions.php';
$insert = array();
/* for($i=1;$i = 2;$i++){
    $insert['payment_amount'] = rand(1000,30000);
    $insert['payment_authority'] = rand(20000000,50000000);
    $insert['payment_refid'] = rand(800000,900000);
    $insert['payment_date'] =get_date();
    $insert['payment_jdate'] =get_date(true);
    $insert['payment_user_id'] = rand(0,1);
    $insert['payment_order_id'] = rand(0,1);
    $insert['payment_status'] = rand(0,1);
    query(queryInsert('payments',$insert));
}

$array = array(1,2,3,4);
$json = json_encode($array);
foreach (json_decode($json,true) as $pr){
    echo $pr;
}

function GetDrivingDistance($lat1_lat2, $long1_long2)
{
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1_lat2."&destinations=".$long1_long2."&mode=driving&language=pl-PL";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
    $dist = str_replace(",",".",$dist);
    return intval($dist); //array('distance' => $dist, 'time' => $time);
}*/
//$distance = str_replace(",",".",GetDrivingDistance("32.409251,48.355590","32.386373,48.414512"));
//echo GetDrivingDistance("32.409251,48.355590","32.397636,48.382817");
/* for($i=1;$i<=10;$i++){
    echo 'Sub ShopsLayout'.$i.'_onCreateViewHolder (Parent As Panel, ViewType As Int)     
	Private logo As ImageView : logo.Initialize("")
	Parent.AddView(logo,70%x,3%y,25%x,25%x)
		
	Private name As Label : name.Initialize("")
	Parent.AddView(name,20%x,5%y,48%x,5%y)
	
	Private address As Label : address.Initialize("")
	Parent.AddView(address,20%x,10%y,48%x,5%y)
	
	Private openclose As Label : openclose.Initialize("")
	Parent.AddView(openclose,2%x,8%y,15%x,5%y)
	
End Sub
Sub ShopsLayout'.$i.'_onBindViewHolder (Parent As Panel, Position As Int) 
	Dim im As ImageView = Parent.GetView(0)
	Glide.Load("http",ShopLogo('.$i.').Get(Position)).Error(function.ErrorLoadingBitmap).CircleCrop.Into(im)
	
	Dim label As Label = Parent.GetView(1)
	label.Text = ShopName('.$i.').Get(Position) : label.Typeface = function.VazirBold : label.TextSize = iSize + 2 : label.TextColor = Colors.Black : label.Gravity = Gravity.Right
	
	Dim label As Label = Parent.GetView(2)
	label.Text = ShopAddress('.$i.').Get(Position) : label.Typeface = function.Vazir : label.TextSize = iSize : label.TextColor = Colors.Gray : label.Gravity = Gravity.Right
	
	Dim label As Label = Parent.GetView(3)
	label.Typeface = function.Vazir : label.TextSize = iSize : label.Gravity = Gravity.Center
	If  ShopOpenClose('.$i.').Get(Position) = "1" Then
		label.Text = cs.Initialize.Color(0xFF076F07).Typeface(Typeface.MATERIALICONS).Append("").Pop.Typeface(function.Vazir).Append("فعال").PopAll
	Else
		label.Text = cs.Initialize.Color(Colors.red).Typeface(Typeface.MATERIALICONS).Append("").Pop.Typeface(function.Vazir).Append("تعطیل").PopAll
	End If
	Parent.Height = 20%y
End Sub

Sub ShopsLayout'.$i.'_GetItemCount As Int 								       
	Return ShopName('.$i.').Size
End Sub

Sub ShopsLayout'.$i.'_ItemClick (ClickedItem As Panel, Position As Int)
	function.iShopId = ShopId('.$i.').Get(Position)
	StartActivity(showShop)
End Sub

';
}*/
function GetDrivingDistance($lat1_lat2, $long1_long2)
{
    $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1_lat2."&destinations=".$long1_long2."&mode=driving&language=pl-PL";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $response = curl_exec($ch);
    curl_close($ch);
    $response_a = json_decode($response, true);
    $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
    //$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
    $dist = str_replace(",",".",$dist);
    return intval($dist); //array('distance' => $dist, 'time' => $time);
}
echo GetDrivingDistance("32.376932,48.4172587","32.4109412,48.3555495");
?>