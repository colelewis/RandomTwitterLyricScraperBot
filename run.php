<?php

require_once "config.php";
require_once "vendor/autoload.php";
use DG\Twitter\Twitter;

define("MAX_TWEET_LENGTH", 280);

function logStr(string $in) : void {
	echo date("c")." ".$in."\n";
}

if (!file_exists(".lyric-cache-".$bandName)) {
	logStr("Run the aggregation script first");
	die();
}

$twitter = new Twitter($consumerKey, $consumerSecret, $oauthToken, $oauthTokenSecret);

$self = $twitter->request("account/verify_credentials", "GET");

if (empty($self->id)) {
	logStr("Could not login with the specified credentials");
	die();
}

logStr("Logged in as @".$self->screen_name);

$lyrics = unserialize(file_get_contents(".lyric-cache-".$bandName));

logStr("Loaded previously aggregated lyrics from ".count($lyrics)." songs");

$lyric = $lyrics[array_rand($lyrics)];

logStr("Searching for 3 line clumps within the song starting with '".substr($lyric,0,30)."'...");

$matches = [];
preg_match_all('/^(?=(.+\n.+\n.+$))/m', $lyric, $matches);

logStr("Found ".count($matches[1])." candidates.");

$i = 0;
shuffle($matches[1]);
while ($i < count($matches[1])) {
	logStr("Trying random lyric ".($i+1));

	$lyric = $matches[1][$i];

	if (strlen($lyric) <= MAX_TWEET_LENGTH) {
		logStr("Sending tweet with lyric: ");

		echo $lyric."\n";

		$twitter->send($lyric);

		logStr("Done!");
		die();
	}

	$i++;
}

logStr("Searching for 2 line clumps within the song");
$matches = [];
preg_match_all('/^(?=(.+\n.+$))/m', $lyric, $matches);

logStr("Found ".count($matches[1])." candidates.");

$i = 0;
shuffle($matches[1]);
while ($i < count($matches[1])) {
	logStr("Trying random lyric ".($i+1));

	$lyric = $matches[1][$i];

	if (strlen($lyric) <= MAX_TWEET_LENGTH) {
		logStr("Sending tweet with lyric: ");

		echo $lyric."\n";

		$twitter->send($lyric);

		logStr("Done!");
		die();
	}

	$i++;
}

logStr("Searching for 1 line clumps within the song");
$matches = [];
preg_match_all('/^(?=(.+$))/m', $lyric, $matches);

logStr("Found ".count($matches[1])." candidates.");

$i = 0;
shuffle($matches[1]);
while ($i < count($matches[1])) {
	logStr("Trying random lyric ".($i+1));

	$lyric = $matches[1][$i];

	if (strlen($lyric) <= MAX_TWEET_LENGTH) {
		logStr("Sending tweet with lyric: ");

		echo $lyric."\n";

		$twitter->send($lyric);

		logStr("Done!");
		die();
	}

	$i++;
}

logStr("Unable to find a suitable lyric.");
