<?php

$url = 'https://api.wynncraft.com/public_api.php?action=guildList';

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
$res = curl_exec($curl);
curl_close($curl);

file_put_contents('guilds.txt', $res);