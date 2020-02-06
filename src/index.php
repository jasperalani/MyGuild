<?php
include 'functions.php';

global $object, $assoc;
$object = json_decode(file_get_contents('../guilds.txt'));
$assoc = json_decode(file_get_contents('../guilds.txt'), true);


# Display guild results
if (isset($_GET['guild']) && !empty($_GET['guild']) ){
    $found = false;
    foreach ($assoc['guilds'] as $key => $guild) {
        if (strpos($guild, $_GET['guild']) || $guild == $_GET['guild']) {
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

displaySearch(['placeholder' => 'Enter a guild name...', 'center']);
listGuilds(0, 0);

?>
</body>
</html>
