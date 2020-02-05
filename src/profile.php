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
<body>
<?php

if (isset($_GET['add'])):

    ?>
    <div id="add-new-form">
        <form method="post" action="">
            <input class="text-area" type="text" name="playername" placeholder="Player name" required>
            <input class="text-area" type="text" name="playeruuid" placeholder="Player uuid">
            <input class="button" name="submit" type="submit" value="add">
        </form>
        <form method="post" action="">
            <input class="button" name="submit" type="submit" value="cancel">
        </form>
    </div>
    <?php

    function addProfile()
    {
        if (isset($_POST['playername'])) {
            $name = $_POST['playername'];
            $file = "profiles/$name.txt";

            global $db;
            $existing = $db->query("SELECT * FROM profiles WHERE name = '$name'");
            if ($existing->num_rows < 1) {
                exec("curl https://api.wynncraft.com/v2/player/$name/stats", $response);
                file_put_contents($file, $response);
                $insert = $db->query("INSERT INTO profiles (name, file) VALUES ('$name', '$file');");
                if ($insert) {
                    echo 'Success!';
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
        <div class="profile-inner row">
            <?php
            foreach ($profiles as $profile) {
                echo "<div class='profile-small col-md-2' data-value='$profile[1]'>$profile[1]</div>";
            }
            ?>
            <div class="col-md-2">
                <form method="get" action="">
                    <input class="button" name="add" type="submit" value="add new">
                </form>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
       $('.profile-small').click(function () {
           window.location.replace('/profile.php?name=' + $(this).attr("data-value"));
       });
    });
</script>
<?php endif; ?>
</body>
</html>