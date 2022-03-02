<?php
/*
----------------------------
  ______      __    __
 /_  __/___ _/ /_  / /__
  / / / __ `/ __ \/ / _ \
 / / / /_/ / /_/ / /  __/
/_/  \__,_/_.___/_/\___/

---------------------------
Table
*/

$table = get_sub_field('table');

if ( $table ) {
    echo '<table border="0">';
        if ( $table['header'] ) {
            echo '<thead>';
                echo '<tr>';
                    foreach ( $table['header'] as $th ) {
                        echo '<th>';
                            echo $th['c'];
                        echo '</th>';
                    }

                echo '</tr>';
            echo '</thead>';
        }

        echo '<tbody>';
            foreach ( $table['body'] as $tr ) {
                echo '<tr>';
                    $th_count = 0;
                    foreach ( $tr as $td ) {
                        echo '<td data-th="'.$table['header'][$th_count]['c'].'">';
                            echo $td['c'];
                        echo '</td>';
                    $th_count++; }
                echo '</tr>';
            }
        echo '</tbody>';
    echo '</table>';
}
?>
