<?php
$phone = (isset($_GET['phone']) && !empty($_GET['phone'])) ? urlencode($_GET['phone']) : urlencode("کاربرتمشک")
;$url = "https://go.crisp.im/chat/embed/?website_id=9b16a266-c80b-4d6e-88aa-32167f7c78d2&user_nickname=$phone&user_email=mhd@gmail.com";
header("Location: $url");
?>