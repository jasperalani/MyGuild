<?php
include 'functions.php';


?>

<html>
<head>
    <title>
        MyGuild Profile Page
    </title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<?php
displaySearch(['center', 'placeholder' => 'Search for a profile...']);

if (isset($_GET['search']) && !empty($_GET['search'])) {
    exec('curl https://api.wynncraft.com/v2/player/' . $_GET['search'] . '/stats', $response);
    $response = json_decode($response[0]);
    $response->message
    if(strpos($response->message, 'bad') !== false){
        echo 'hi';
    }
}
?>
</body>
</html>

