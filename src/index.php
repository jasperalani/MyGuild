<?php
global $object, $assoc;
$object = json_decode( file_get_contents( '../guilds.txt' ) );
$assoc  = json_decode( file_get_contents( '../guilds.txt' ), true );

function listGuilds( $e = 0, $order = 0 ) {
	global $assoc;
	if ( $order ) {
		$assoc['guilds'] = array_reverse( $assoc['guilds'] );
	}
	if ( $e > 0 ) {
		foreach ( $assoc['guilds'] as $key => $guild ) {
			echo "<span class='guild'><a href='?search=$guild'>$guild</a></span>";
			if ( $key > $e ) {
				break;
			}
		}
	} else {
		foreach ( $assoc['guilds'] as $guild ) {
			echo "<span class='guild'><a href='?search=$guild'>$guild</a></span>";
		}
	}
}

function displaySearch() {
	?>
    <div id="search-container">
        <div id="search-box">
            <form method="get" action="">
                <input type="text" name="search" placeholder="Enter a guild name...">
                <input type="submit" value="Search">
            </form>
        </div>
    </div>
	<?php
}

function displayGuildInfo( $name ) {
	if ( ! $name || empty( $name ) ) {
		return 0;
	}

	$url  = 'https://api.wynncraft.com/public_api.php?action=guildStats&command=' . $name;
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_URL, $url );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	$res = curl_exec( $curl );
	curl_close( $curl );

	$res = json_decode( $res, true );

	$year      = substr( $res['created'], 0, 4 );
	$mon       = substr( $res['created'], 5, 2 );
	$dateObj   = DateTime::createFromFormat( '!m', $mon );
	$monthName = $dateObj->format( 'F' );#

	?>

    <div id="search-results">
        <h2><?= $res['name'] ?></h2>
        <h3><?= $res['prefix'] ?></h3>
        <p>current level: <?= $res['level'] ?></p>
        <p>xp to next level: <?= $res['xp'] ?></p>
        <p>territories owned: <?= $res['territories'] ?></p>
        <p>created in <?= ( $monthName . " " . $year ) ?></p>
        <div id="gmembers">
			<?php
			exec( 'rm -R faces' );
			exec( 'mkdir faces' );

			foreach ( $res['members'] as $key => $member ) {
				smallProfile( $member );
			}

			?>
        </div>
    </div>
	<?php


	return 1;
}

function smallProfile( $member ) {

	$new_date = date( 'jS F Y', strtotime( $member['joined'] ) );
	$small    = date( 'h:i:s', strtotime( $member['joined'] ) );

	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_URL, 'https://crafatar.com/avatars/' . $member['uuid'] . '?size=50' );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	$res = curl_exec( $curl );
	curl_close( $curl );
	file_put_contents( 'faces/' . $member['name'] . '.png', $res );

	?>

    <div class="profile">
        <img src="faces/<?= $member['name'] ?>.png">
        <div class="namerank">
            <span class="name"><?= $member['name'] ?></span><br>
            <small class="rank <?= $member['rank'] ?>"><i><?= strtolower( $member['rank'] ) ?></i></small><br><br>
        </div>
        <small><?= $member['contributed'] ?> xp contributed</small><br>
        <small class="showuuid">show uuid</small>
        <small class="uuid"><?= $member['uuid'] ?></small>
        <small class="showjoined">show joined</small>
        <small class="joined"><?= $small . '<br>' . $new_date ?></small>
    </div>

	<?php
}

if ( isset( $_GET['search'] ) ) {
	if ( ! empty( $_GET['search'] ) ) {
		$found = false;
		foreach ( $assoc['guilds'] as $key => $guild ) {
			if ( strpos( $guild, $_GET['search'] ) || $guild == $_GET['search'] ) {
				displayGuildInfo( $guild );
				//https://api.wynncraft.com/public_api.php?action=guildStats&command=DankMenHQ
				$found = true;
			}
		}
		if ( ! $found ) {
			echo 'nothing found';
		}
		echo '<hr>';
	}

}

?>

<html>
<head>
    <title>
        MyGuild
    </title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>

<?php

displaySearch();
listGuilds( 400, 1 );

?>
</body>
</html>
