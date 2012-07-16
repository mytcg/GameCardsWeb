<?php
 
    function DateSelector($inName, $useDate=0) 
    { 
        /* create array so we can name months */ 
        $monthName = array(1=> "January", "February", "March", 
            "April", "May", "June", "July", "August", 
            "September", "October", "November", "December"); 
 
        /* if date invalid or not supplied, use current time */ 
        if($useDate == 0) 
        { 
            $useDate = time(); 
        } 
 
        /* make month selector */ 
        echo "<SELECT NAME=" . $inName . "Month>\n"; 
        for($currentMonth = 1; $currentMonth <= 12; $currentMonth++) 
        { 
            echo "<OPTION VALUE=\""; 
            echo intval($currentMonth); 
            echo "\""; 
            if(intval(date( "m", $useDate))==$currentMonth) 
            { 
                echo " SELECTED"; 
            } 
            echo ">" . $monthName[$currentMonth] . "\n"; 
        } 
        echo "</SELECT>"; 
 
        /* make day selector */ 
        echo "<SELECT NAME=" . $inName . "Day>\n"; 
        for($currentDay=1; $currentDay <= 31; $currentDay++) 
        { 
            echo "<OPTION VALUE=\"$currentDay\""; 
            if(intval(date( "d", $useDate))==$currentDay) 
            { 
                echo " SELECTED"; 
            } 
            echo ">$currentDay\n"; 
        } 
        echo "</SELECT>"; 
 
        /* make year selector */ 
        echo "<SELECT NAME=" . $inName . "Year>\n"; 
        $startYear = date( "Y", $useDate); 
        for($currentYear = $startYear; $currentYear <= $startYear+10;$currentYear++) 
        { 
            echo "<OPTION VALUE=\"$currentYear\""; 
            if(date( "Y", $useDate)==$currentYear) 
            { 
                echo " SELECTED"; 
            } 
            echo ">$currentYear\n"; 
        } 
        echo "</SELECT>"; 
 
    } 
?> 
 
<HTML> 
<BODY> 
<FORM> 
Choose a Date: <?php DateSelector( "Sample"); ?> 
</FORM> 
</BODY> 
</HTML>