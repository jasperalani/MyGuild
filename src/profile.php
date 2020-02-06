<?php
include 'functions.php';
global $db;

# Add a new profile

?>

<html>
<head>
    <title>
        MyGuild Profile Page
    </title>
    <?php include_once 'links.php'; ?>
</head>
<body id="profile-body">
<?php

if (isset($_GET['add'])):

    ?>
    <div id="add-new-form">
        <form method="post" action="">
            <input class="text-area" type="text" name="playername" placeholder="Player name" required>
            <input class="text-area" type="text" name="playeruuid" placeholder="Player uuid">
            <input class="button" name="submit" type="submit" value="add">
            <input id="add-profile-cancel-button" class="button" name="submit" type="submit" value="cancel">
        </form>
    </div>
<?php

function addProfile()
{
    if (isset($_POST['playername'])) {
        $name = $_POST['playername'];
        $file = "profiles/$name.txt";

        exec("curl https://api.mojang.com/users/profiles/minecraft/$name", $response);
        $response = json_decode($response[0]);
        $uuid = $response->id;

        global $db;
        $existing = $db->query("SELECT * FROM profiles WHERE name = '$name'");
        if ($existing->num_rows < 1) {
            exec("curl https://api.wynncraft.com/v2/player/$uuid/stats", $response);
            file_put_contents($file, $response);
            $insert = $db->query("INSERT INTO profiles (name, uuid, file) VALUES ('$name', '$uuid', '$file');");
            if ($insert) {
                header("Location: /profile.php");
            }
        } else {
            echo 'Profile already exists';
        }
    }
}

if (isset($_POST['submit'])):
    switch ($_POST['submit']) {
        case 'add':
            addProfile();
            break;
        case 'cancel':
            header("Location: /profile.php");
            break;
    }
endif;


else:

# Check for profiles
$profiles = array();
$profiles_ = $db->query("SELECT * FROM profiles");
if ($profiles_) {
    while ($row = $profiles_->fetch_row()) {
        $profiles[] = $row;
    }
}
//displaySearch(['center', 'placeholder' => 'Search for a profile...']);
?>

    <div id="profile-wrapper">
        <h1>Profiles</h1>

        <div class="grid">
            <?php
            foreach ($profiles as $profile) {
                $check = checkForUUID($profile) ? checkForPicture($profile) : false;
                echo !$check ? 'Error updating profiles' : '';

                if($check) {
                    $picture = "profiles/pictures/$profile[1].png";
                    $img = "<img src='$picture'>";
                }

                echo "<div>$img<h5>$profile[1]</h5></div>";
            }
            ?>
            <div id="add-new">add new</div>
        </div>

    </div>

    <script>
        $(document).ready(function () {
            $('.profile-small').click(function () {
                console.log(val($(this).innerHTML));
                // window.location.replace('/profile.php?name=' + $(this).attr("data-value"));
            });
        });
    </script>
<?php endif; ?>
</body>
</html>