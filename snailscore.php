<?php

$scorepass = "xxxx";
$dbserver = "localhost";
$dbuser = "magicsnail";
$dbname = "magicsnail";
$dbpass = "zzz";

$password = $_GET["password"];
if (!isset($password) || !($password == $scorepass)) {
    die('Error 1');
}

$mymode = $_GET["mode"];
if (!isset($mymode)) {
    die('Error 2');
}
if ($mymode != "get" && $mymode != "set") {
    die('Error 3');
}

if ($mymode == "set") {
    $score = $_GET["score"];
    if (!isset($score) || !is_numeric($score)) {
        die('Error 4');
    }

    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Error 61");
    }

	$stmt = $conn->prepare("INSERT INTO scores (score) VALUES (?)");
	$stmt->bind_param("d", $score);
	$stmt->execute();
	$stmt->close();
	
	$yourplace = $conn->prepare("select count(*) from scores where score > ?");
	$yourplace->bind_param("d", $score);
	$yourplace->execute();
	$yourplace->bind_result($place);
	$yourplace->fetch();
	$yourplace->close();
	
	echo (int)$place+1;
	
	$conn->close();
} else {
    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Error 62");
    }

	$allscores = "select score from scores order by score desc LIMIT 12";
	$allscores = $conn->query($allscores);
	
	while($scorerow = $allscores->fetch_assoc()) {
		echo $scorerow["score"] . ";";
	}

	$allscores->close();
	$conn->close();
}

/*
CREATE TABLE `scores` (
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

http://tclinux.de/snailscore.php?password=xxx&mode=get
http://tclinux.de/snailscore.php?password=xxx&mode=set&score=5600
*/
?>