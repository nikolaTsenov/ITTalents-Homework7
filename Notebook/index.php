<?php 
function myErrorHandler($errNumber,$errMsg) {
	if ($errNumber == E_WARNING || $errNumber == E_NOTICE) {
		echo "<p>Sth went slightly out of the true path.</p>";
		echo "<p>Continue with script.</p>";
	}
	if ($errNumber == E_ERROR || $errNumber == E_RECOVERABLE_ERROR) {
		echo "<p>Sth went terribly wrong.</p>";
		echo "<p>We will have to close. Sorry for the inconvenience!</p>";
		fclose($handle0);
		fclose($handle1);
		die();
	}
}
set_error_handler('myErrorHandler');
$handle0 = fopen ( './notes.txt', 'r' );
$notesCount = 0;
while ( ! feof ( $handle0 ) ) {
	$counterOfNotes = fgets ( $handle0 );
	$notesCount++;
}
//echo $notesCount;
fclose ( $handle0 );
$legitNumberOfNotes = true;
if (isset($_POST['submit']) || isset($_POST['laterPriority'])) {
	if ($notesCount > 99) {
		$legitNumberOfNotes = false;
	}
}
$legitData = true;
$legitPriority = true;
if (isset($_POST['submit'])) {
	$newNote = htmlentities(trim($_POST['note']));
	$priority = htmlentities(trim($_POST['priority']));
	if (mb_strlen($newNote,"UTF-8") < 3 || mb_strlen($newNote,"UTF-8") > 50) {
		$legitData = false;
	}
	if ($priority > 5 || $priority < 1) {
		$legitPriority = false;
	}
} else {
	$newNote = "";
	$priority = "";
}
$delNoteLegit = true;
$delNoteLegit2 = true;
if (isset($_POST['delete'])) {
	$delNote = htmlentities(trim($_POST['delNote']));
	if (!is_numeric($delNote)) {
		$delNoteLegit = false;
	} else {
		$content = file_get_contents("./notes.txt");
		//echo $content;
		$array = explode("\n", $content);
		//var_dump($array);
		if ($delNote > count($array) || $delNote < 1){
			$delNoteLegit2 = false;
		} else {
			foreach ($array as $key=>$value) {
				if ($delNote-1 == $key) {
					array_splice($array,$key,1);
				}
			}
			$newContent = implode("\n",$array);
			file_put_contents('./notes.txt', $newContent);
		}
	}
} else {
	$delNote = "";
}
$showHideCount = 1;
if (isset($_POST['showRows'])) {
	$showHideCount = $_POST['showHide'] + 1;
}
if (isset($_POST['submit']) || isset($_POST['delete']) || isset($_POST['submitAddedPrio']) ||
		isset($_POST['laterPriority']) || isset($_POST['dontContinue'])) {
	$showHideCount = $_POST['showHide'] + 2;
}
$validNewPrio = true;
$validLineForNewPrio = true;
if (isset($_POST['submitAddedPrio'])) {
	$newPrioLine = htmlentities(trim($_POST['addLinePrio']));
	$newPrio = htmlentities(trim($_POST['newLinePrio']));
	if ($newPrio > 5 || $newPrio < 1) {
		$validNewPrio = false;
	} else {
		$cont = file_get_contents('./notes.txt');
		$contArr = explode("\n",$cont);
		//var_dump($contArr);
		if ($newPrioLine > count($contArr) || $newPrioLine < 1) {
			$validLineForNewPrio = false;
		} else {
			$ab = "";
			for ($p = 0; $p < count($contArr); $p++) {
				if ($newPrioLine - 1 == $p) {
					//echo $st; echo "<br />";
					for ($z = mb_strlen($contArr[$p],"UTF-8") - 2; $z <= mb_strlen($contArr[$p],"UTF-8") - 1; $z++) {
						$ab .= $contArr[$p]{$z};
					}
					if ($ab == "#1" || $ab == "#2" || $ab == "#3" || $ab == "#4" || $ab == "#5") {
						$contArr[$p] = str_replace($ab,"#$newPrio",$contArr[$p]);
					} else {
						$contArr[$p] .= "#$newPrio";
					}
				}
			}
			$newCont = implode("\n",$contArr);
			//var_dump($newCont);
			file_put_contents('./notes.txt', $newCont);
		}
		
	}
} else {
	$newPrioLine = "";
	$newPrio = "";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>For Test 2</title>
<link rel="stylesheet" href="./assets/css/styles.css" type="text/css" />
</head>
<body>
	<fieldset>
		<form action="./index.php" method="post">
			<label for="note">Write Your New Note: </label> 
			<input id="note" type="text" name="note" value="<?php echo $newNote; ?>" /> 
			<label for="priority">Set the note priority(from 1 to 5): </label> 
			<input id="priority" type="text" name="priority" value="<?php echo $priority; ?>" />
			<input name="submit" type="submit" value="Submit Your Note" />
			<br />
			<br />
			<label for="delNote">Write the number of the note you want deleted(<input name="showRows" type="submit" value="Show/Hide Notes' Numeration" />): </label> 
			<input id="delNote" type="text" name="delNote" value="<?php echo $delNote; ?>" /> 
			<input name="delete" type="submit" value="delete your note" />
			<input name="showHide" type="hidden" value="<?php echo $showHideCount; ?>" />
			<br />
			<br />
			<label for="addLinePrio">Which note's priority you want added/changed:</label> 
			<input id="addLinePrio" name="addLinePrio" type="text" value="<?php echo $newPrioLine; ?>" />
			<label for="newLinePrio">What is the new priority:</label> 
			<input id="newLinePrio" name="newLinePrio" type="text" value="<?php echo $newPrio; ?>" />
			<input name="submitAddedPrio" type="submit" value="Submit the new priority" />
			<br />
			<br />
			<a href="./index2.php" target="_blank">See your sorted by priority notes.</a><br />
			<?php 
				if (!$legitNumberOfNotes) {
			?>
				<div><p class="warning">You can't have more than 100 notes!<input name="reload" type="submit" value="Reload" /></p></div>
			<?php 
				} 
			?>
			<?php 
				if (!$legitData) {
			?>
				<div><p class="warning">Your note must be at least 3 symbols and up to 50 symbols!<input name="reload" type="submit" value="Reload" /></p></div>
			<?php 
				} 
			?>
			<?php 
				if (!$legitPriority && $priority == "" && $legitData) {
			?>
				<div><p class="warning" >Are you sure you want to leave your note without priority?</p>
					<input name="laterPriority" type="submit" value="Leave Priority For Later" />
					<input name="dontContinue" type="submit" value="I don't want to continue" />
				</div>
			<?php 
				}
				if (!$legitPriority && $priority !== "") {
			?>
				<div><p class="warning" >You must enter a number from 1 to 5 for priority!</p></div>
			<?php 
				}
				if (!$delNoteLegit) {
			?>
				<div><p class="warning" >Enter a single number that is the number of the row of the note you want deleted!</p></div>
			<?php 
				} 
			?>
			<?php 
				if (!$delNoteLegit2) {
			?>
				<div><p class="warning" >You have entered <strong><?php echo $delNote; ?></strong> while the last number of your notes is <strong><?php echo count($array); ?></strong></p></div>
			<?php 
				} 
			?>
			<?php 
				if (!$validNewPrio) {
			?>
				<div><p class="warning" >The new priority must be between 1 and 5!</p></div>
			<?php 
				} 
			?>
			<?php 
				if (!$validLineForNewPrio) {
			?>
				<div><p class="warning" >The line you have entered is invalid!</p></div>
			<?php 
				} 
			?>
		</form>
	</fieldset>
			<?php
			if ($legitData && $legitNumberOfNotes) {
				$handle1 = fopen ( './notes.txt', 'r+' );
				$lineArray = array ();
				while ( ! feof ( $handle1 ) ) {
					$line = fgets ( $handle1 );
					$lineArray [] = $line;
				}
				if (isset ( $_POST ['submit'] ) && $legitPriority) {
					$lineArray [] = $newNote;
					fwrite ( $handle1, "\n" . $newNote . '#' . $priority );
				}
				if (isset ( $_POST ['laterPriority'] )) {
					$newNote = htmlentities(trim($_POST['note']));
					$lineArray [] = $newNote;
					fwrite ( $handle1, "\n" . $newNote );
				}
				if ($showHideCount % 2 == 0) {
					echo "<ol>";
				} else {
					echo "<ul>";
				}
				foreach ( $lineArray as $line ) {
					if ($line === $newNote) {
						if (isset ( $_POST ['laterPriority'] )) {
							echo "<li>$line</li>";
						} else {
							echo "<li>$line &#35;$priority <img src='./assets/images/img$priority.jpg' alt='' /></li>";
						}
					} else {
						if (substr_count($line, "#1", mb_strlen($line,"UTF-8")-3) == 1) {
							echo "<li>$line <img src='./assets/images/img1.jpg' alt='' /></li>";
						} elseif (substr_count($line, "#2", mb_strlen($line,"UTF-8")-3) == 1) {
							echo "<li>$line <img src='./assets/images/img2.jpg' alt='' /></li>";
						} elseif (substr_count($line, "#3", mb_strlen($line,"UTF-8")-3) == 1) {
							echo "<li>$line <img src='./assets/images/img3.jpg' alt='' /></li>";
						} elseif (substr_count($line, "#4", mb_strlen($line,"UTF-8")-3) == 1) {
							echo "<li>$line <img src='./assets/images/img4.jpg' alt='' /></li>";
						} elseif (substr_count($line, "#5", mb_strlen($line,"UTF-8")-3) == 1) {
							echo "<li>$line <img src='./assets/images/img5.jpg' alt='' /></li>";
						} else {
							echo "<li>$line</li>";
						}
					}
				}
				if ($showHideCount % 2 == 0) {
					echo "</ol>";
				} else {
					echo "</ul>";
				}
				// var_dump($lineArray);
				fclose ( $handle1 );
			}
			?>
</body>
</html>