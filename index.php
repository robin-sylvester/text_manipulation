<!DOCTYPE html>
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="text.css">
</head>

<body>
<h2 class="center title">Welcome to TWA</h2>
<h5 class="center upper">Transforming text Web Application</h5>
<br />

<div class="default center">

<form action="query.php" method="post">
<textarea rows="20" cols="70" name="text" maxlength="1000">
<?php
$m = new Memcached();
$m->addServer('web_server_ip_address', 11211);
if ($m->get('original'))
	echo $m->get('original');
else
	echo 'Write here..';
?>
</textarea>
<br />
<button type="submit" value="Submit" class="button">Submit</button>
<button type="reset" value="Reset" class="button" onclick="location.href='query.php?flush=on';">Reset</button>
</form>

<br />
<br />

<form action="query.php" method="post">
<textarea rows="20" cols="70" name="result" maxlength="1000">
<?php
$m = new Memcached();
$m->addServer('web_server_ip_address', 11211);
if ($m->get('text'))
    echo $m->get('text');
else
    echo 'Result here..';
?>
</textarea>
<br />
<input type="submit" name="first" class="button2" value="1. transformation" />
<input type="submit" name="second" class="button2" value="2. transformation" />
<input type="submit" name="third" class="button2" value="3. transformation" />
<input type="submit" name="fourth" class="button2" value="4. transformation" />
<input type="submit" name="statistics" class="button3" value="Statistics" />
</form>

<br />
<br />

<?php
if(isset($_GET['sentences']) && isset($_GET['vowels']) && isset($_GET['a']) && isset($_GET['e']) && isset($_GET['i']) && isset($_GET['o']) && isset($_GET['u'])){

    if (($_GET['sentences'] != 0) && ($_GET['vowels'] != 0)){

        echo "<div class='statistics'>";
        echo "<h3> Text statistics: </h3>";

        echo "<p class='down'> By vowel: </p>";
        echo "<ul>";
        echo "<li> Letter a: <b>" . number_format($_GET['a']/$_GET['vowels']*100, 2) . "</b> %</li>";
        echo "<li> Letter e: <b>" . number_format($_GET['e']/$_GET['vowels']*100, 2) . "</b> %</li>";
        echo "<li> Letter i: <b>" . number_format($_GET['i']/$_GET['vowels']*100, 2) . "</b> %</li>";
        echo "<li> Letter o: <b>" . number_format($_GET['o']/$_GET['vowels']*100, 2) . "</b> %</li>";
        echo "<li> Letter u: <b>" . number_format($_GET['u']/$_GET['vowels']*100, 2) . "</b> %</li>";
        echo "</ul>";

        echo "<br />";

        echo "<p class='down'> By sentences: </p>";
        echo "<ul>";
        echo "<li> Letter a is approximately found in <b>" . number_format($_GET['a']/$_GET['sentences']*100, 2) . "</b> % sentences</li>";
        echo "<li> Letter e is approximately found in <b>" . number_format($_GET['e']/$_GET['sentences']*100, 2) . "</b> % sentences</li>";
        echo "<li> Letter i is approximately found in <b>" . number_format($_GET['i']/$_GET['sentences']*100, 2) . "</b> % sentences</li>";
        echo "<li> Letter o is approximately found in <b>" . number_format($_GET['o']/$_GET['sentences']*100, 2) . "</b> % sentences</li>";
        echo "<li> Letter u is approximately found in <b>" . number_format($_GET['u']/$_GET['sentences']*100, 2) . "</b> % sentences</li>";
        echo "</ul>";
        echo "<br />";
        echo "</div>";
    }
}
?>

</div>

<div class="footer">
  <p>Created by: Your name</p>
  Contact information: <a href="email_address">email</a></p>
</div>

</body>
</html>

