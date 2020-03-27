<?php

require_once "config.php";
require_once "vendor/autoload.php";

function logStr(string $in) : void {
	echo date("c")." ".$in."\n";
}

logStr("Starting aggregation");

$url = "https://lyrics.fandom.com/wiki/Category:Songs_by_".$bandName;
logStr('Getting URL "'.$url.'"');

$data = file_get_contents($url);

$dom = pQuery::parseStr($data);

$songList = $dom->query(".category-page__member");

logStr("Got ".count($songList)." songs");

$lyrics = [];

foreach ($songList as $songElement) {
	$a = $songElement->query("a");

	$url = "https://lyrics.fandom.com".$a->attr("href");

	logStr("Loading lyrics for ".$a->attr("title")." from '".$url."'");

	$lyricPageData = file_get_contents($url);
	$lyricPageDom = pQuery::parseStr($lyricPageData);

	$lyric = $lyricPageDom->query(".lyricbox");
	$lyricText = $lyric->html();
	$lyricText = str_replace("<br />", "\n", $lyricText);
	$lyricText = trim(strip_tags($lyricText));

	$lyrics[] = $lyricText;
}

logStr("Lyric downloading completed");

file_put_contents(".lyric-cache-".$bandName, serialize($lyrics));

logStr("Wrote ".count($songList)." songs in ".filesize(".lyric-cache-".$bandName)."B to '.lyric-cache-".$bandName."'");
logStr("Done!");
