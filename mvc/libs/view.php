<?php

function show_table($rows){
	echo '<table border="1"';
	
	echo '<tr>';
		foreach ($rows[0] as $fieldname => $fieldvalue) {
			echo "<th>$fieldname</th>";
		}
	echo '</tr>';

	foreach ($rows as $row) {
		echo '<tr>';
		foreach ($row as $fieldname => $fieldvalue) {
			echo "<td>$fieldvalue</td>";
		}
		echo '</tr>';
	}

	echo '</table>';
}