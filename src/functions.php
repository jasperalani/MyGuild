<?php

global $db;

# Get the database
function getDB()
{
    $mysqli = new mysqli('localhost', 'root', '', 'myguild', '3307');
    if ($mysqli->connect_errno) {
        echo "mysql err\n";
        echo "Errno: " . $mysqli->connect_errno . "\n";
        echo "Error: " . $mysqli->connect_error . "\n";
        exit;
    }
    global $db;
    $db = $mysqli;
    return $mysqli;
}

getDB();

# List guilds
function listGuilds($length = 0, $order = 0)
{
    global $assoc;
    $assoc['guilds'] = $order ? array_reverse($assoc['guilds']) : $assoc['guilds'];

    echo '<div id="guild-list">';

    if ($length > 0) {
        $array = $assoc['guilds'];
        $array = array_slice($array, 0, $length);

        foreach ($array as $key => $guild) {

            echo "<span class='guild'><a href='?guild=$guild'>$guild</a></span>";
            if ($key > $length) {
                break;
            }
        }
    } else {
        foreach ($assoc['guilds'] as $guild) {
            echo "<span class='guild'><a href='?guild=$guild'>$guild</a></span>";
        }
    }

    echo '</div>';
}

# Display search bar
function displaySearch($options = null)
{
    $center = isset($options) && in_array('center', $options) ? 'center' : '';
    $placeholder = isset($options) && array_key_exists('placeholder', $options) ? $options['placeholder'] : '';
    ?>
    <div id="search-container">
        <div id="search-box" class="<?= $center ?>">
            <form method="get" action="">
                <input type="text" name="search" placeholder="<?= $placeholder ?>">
                <input class="button" type="submit" value="Search">
            </form>
        </div>
    </div>
    <?php
}

# Display guild info
function displayGuildInfo($name)
{
    if (!$name || empty($name)) {
        return 0;
    }
//var_dump('https://api.wynncraft.com/public_api.php?action=guildStats&command=' . urlencode($name));exit;
    exec('curl https://api.wynncraft.com/public_api.php?action=guildStats&command=StormKnights', $response);
    var_dump($response[0]);
    exit;

    $res = json_decode($res, true);
    var_dump($res);
    exit;

    $year = substr($res['created'], 0, 4);
    $mon = substr($res['created'], 5, 2);
    $dateObj = DateTime::createFromFormat('!m', $mon);
    $monthName = $dateObj->format('F');#

    ?>

    <div id="search-results">
        <div id="ginfo">
            <h2><?= $res['name'] ?></h2>
            <h3><?= $res['prefix'] ?></h3>
            <p>current level: <?= $res['level'] ?></p>
            <p>xp to next level: <?= $res['xp'] ?></p>
            <p>territories owned: <?= $res['territories'] ?></p>
            <p>created in <?= ($monthName . " " . $year) ?></p>
        </div>
        <div id="gmembers">
            <?php createOrderedProfiles($res); ?>
        </div>
    </div>
    <?php

    return 1;
}

# Small profile
function smallProfile($member)
{
    $img_src = 'https://minotar.net/avatar/' . $member['name'] . '/50.png';
    $new_date = date('jS F Y', strtotime($member['joined']));
    $small = date('h:i:s', strtotime($member['joined']));
    ?>
    <div class="profile">
        <img src="<?= $img_src ?>">
        <div class="namerank">
            <span class="name"><?= $member['name'] ?></span><br>
            <small class="rank <?= $member['rank'] ?>"><i><?= strtolower($member['rank']) ?></i></small>
            <br><br>
        </div>
        <small><?= $member['contributed'] ?> xp contributed</small>
        <br>
        <small class="showuuid">show uuid</small>
        <small class="uuid"><?= $member['uuid'] ?></small>
        <small class="showjoined">show joined</small>
        <small class="joined"><?= $small . '<br>' . $new_date ?></small>
    </div>

    <?php
    return $member['rank'];
}

# List profiles in ranking order
function createOrderedProfiles($res)
{
    foreach ($res['members'] as $key => $member) {
        $ranks[strtolower($member['rank'])][] = $key;
    }
    smallProfile($res['members'][$ranks['owner'][0]]);
    foreach ($ranks['chief'] as $chiefs) {
        smallProfile($res['members'][$chiefs]);
    }
    foreach ($ranks['captain'] as $captains) {
        smallProfile($res['members'][$captains]);
    }
    foreach ($ranks['recruiter'] as $recruiters) {
        smallProfile($res['members'][$recruiters]);
    }
    foreach ($ranks['recruit'] as $recruits) {
        smallProfile($res['members'][$recruits]);
    }
}

# UUID Check
# @return string|null
function checkForUUID($profile)
{
    if (!is_array($profile)) {
        return false;
    }

    if (empty($profile[2])) {
        exec('curl https://api.mojang.com/users/profiles/minecraft/' . $profile[1], $response);
        $object = json_decode($response[0]);
        $uuid = $object->id;
        $name = $object->name;
        if (empty($uuid) || strlen($uuid) < 32) {
            return false;
        }
        $db = getDB();
        $query = "UPDATE profiles SET uuid = '$uuid' WHERE name = '$name'";
        $update = $db->query($query);
        if ($update) {
            return $uuid;
        } else {
            return false;
        }
    }else{
        return $profile[2];
    }
}

function checkForPicture($profile)
{
    if(empty($profile[4])){
        if(empty($profile[1])){
            return false;
        }
        $picture = "profiles/pictures/$profile[1].png";
        if(file_exists($picture)){
            return true;
        }else{
            if(empty($profile[2])){
                return false;
            }else{
                exec("curl https://crafatar.com/avatars/$profile[2]?size=80 > profiles/pictures/$profile[1].png");
                return true;
            }
        }
    }
}

//array(4) { [0]=> string(1) "2" [1]=> string(5) "Grian" [2]=> NULL [3]=> string(18) "profiles/Grian.txt" }