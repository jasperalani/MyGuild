<?php

#
$player = 'Kikis';
$url = "https://api.wynncraft.com/v2/player/$player/stats";

exec('curl '.$url, $response);
file_put_contents($player.'.txt', $response);