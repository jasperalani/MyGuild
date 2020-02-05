<?php
include 'functions.php';

global $object, $assoc;
$object = json_decode(file_get_contents('../guilds.txt'));
$assoc = json_decode(file_get_contents('../guilds.txt'), true);


# Display search results
if (isset($_GET['search']) && !empty($_GET['search']) ){
    $found = false;
    foreach ($assoc['guilds'] as $key => $guild) {
        if (strpos($guild, $_GET['search']) || $guild == $_GET['search']) {
            displayGuildInfo($guild);
            $found = true;
        }
    }
    if (!$found) {
        echo 'nothing found';
    }
    echo '<hr>';
}

?>

<html>
<head>
    <title>
        MyGuild
    </title>
    <?php include_once 'links.php'; ?>
</head>
<body>
<?php

displaySearch(['placeholder' => 'Enter a guild name...']);
listGuilds(400, 0);

?>
</body>
</html>
