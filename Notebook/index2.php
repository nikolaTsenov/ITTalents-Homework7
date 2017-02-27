<?php
$c = file_get_contents('./notes.txt');
$arr = explode("\n",$c);
//var_dump($arr);
//echo "<br />";
$prioArr = array();
$combinedArr = array();
foreach ($arr as $key=>$value) {
	for ($i = mb_strlen(($arr[$key]),"UTF-8") - 1; $i > mb_strlen(($arr[$key]),"UTF-8") - 3; $i--) {
			$a = $arr[$key]{$i};
			if ($i == mb_strlen (($arr [$key]), "UTF-8" ) - 1) {
				if ($a > 0 && $a < 6) {
					$prioArr[] = $a;
				} else {
					$prioArr[] = "not given";
				}
			}
			if (($a > 0 && $a < 6 && $arr[$key]{$i-1} == "#") || $a == "#") {
				$arr[$key]{$i} = "";
			}
	}
}
$combinedArr = array_combine($arr,$prioArr);
asort($combinedArr);
//echo "<br />";
//var_dump($combinedArr);
sort($prioArr);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>For Test 2 - sorted notes</title>
<link rel="stylesheet" href="./assets/css/styles.css" type="text/css" />
</head>
<body>
	<table id="table">
		<thead>
			<tr>
				<th>Priority</th>
				<th>Note</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			for ($index = 0; $index < count($prioArr); $index++) {
					echo "<tr>";
					echo "<td class='td$prioArr[$index]'>" . $prioArr[$index] . "</td>";
					foreach($combinedArr as $k=>$v) {
						echo "<td class='td$prioArr[$index]'>" . $k . "</td>";
						array_shift($combinedArr);
						break;
					}
					echo "</tr>";
			}
		?>
		</tbody>
	</table>
	<br />
	<a href="./index.php" >Go back to homepage</a>
</body>
</html>