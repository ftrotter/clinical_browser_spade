<?php

echo "
<html><head></head><body>
<h1> Send some wikitext to the parsoid processor </h1>
<form action='http://parsoid-lb.eqiad.wikimedia.org/enwiki/' method='POST'>
<textarea cols='100' rows='30' name='wt' id='wt'>
</textarea>
<br>
Set to 1 for just returning the body without html headers: <b>Body</b> <input name='body' type='text' value='1'><br>
<input name='submit' type='submit' value='submit'>
</form>
</body></html>
";


?>
