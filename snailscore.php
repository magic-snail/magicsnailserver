<?php

$dbserver = "localhost";
$dbuser = "magicsnail";
$dbname = "magicsnail";
$dbpass = "EArCzlHOgKhi19gM";

$mymode = $_GET["mode"];
if (!isset($mymode)) {
    die('Error 2');
}
if ($mymode != "get" && $mymode != "set" && $mymode != "redirecttobadge") {
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

	$place = (int)$place+1;

	$totalscores = $conn->prepare("select count(*) from scores");
	$totalscores->execute();
	$totalscores->bind_result($total);
	$totalscores->fetch();
	$totalscores->close();

    echo $place . "/" . $total;

	$conn->close();
}

if ($mymode == "get") {
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

if ($mymode == "redirecttobadge") {
    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Error 63");
    }

    $highestscore = $conn->prepare("select max(score) from scores");
	$highestscore->execute();
	$highestscore->bind_result($score);
	$highestscore->fetch();
	$highestscore->close();

    header('Location: https://img.shields.io/badge/highscore-' . $score . '-success');

	$allscores->close();
	$conn->close();
}

/*
CREATE TABLE `scores` (
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

http://tclinux.de/snailscore.php?mode=get
http://tclinux.de/snailscore.php?mode=redirecttobadge
http://tclinux.de/snailscore.php?mode=set&score=5600
*/
