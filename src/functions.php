<?php

# List guilds
function listGuilds($length = 0, $order = 0)
{
    global $assoc;
    $assoc['guilds'] = $order ? array_reverse($assoc['guilds']) : $assoc['guilds'];
    if ($length > 0) {
        $array = $assoc['guilds'];
        $array = array_slice($array, 0, $length);
        echo '<div id="guild-list">';
        foreach ($array as $key => $guild) {
            echo "<span class='guild'><a href='?search=$guild'>$guild</a></span>";
            if ($key > $length) {
                break;
            }
        }
        echo '</div>';
    } else {
        foreach ($assoc['guilds'] as $guild) {
            echo "<span class='guild'><a href='?search=$guild'>$guild</a></span>";
        }
    }
}

# Display search bar
function displaySearch($options = null){
    $center = isset($options) && in_array('center',  $options) ? 'center' : '';
    $placeholder = isset($options) && array_key_exists('placeholder', $options) ? $options['placeholder'] : '';
    ?>
    <div id="search-container">
        <div id="search-box" class="<?= $center ?>">
            <form method="get" action="">
                <input type="text" name="search" placeholder="<?= $placeholder ?>">
                <input type="submit" value="Search">
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

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, 'https://api.wynncraft.com/public_api.php?action=guildStats&command=' . urlencode($name));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($curl);
    curl_close($curl);

    $res = json_decode($res, true);

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