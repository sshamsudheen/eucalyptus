<?php

###################################################################################################################
############						CHECK ALL THE BACKUPS HAPPENING PERIODICALLY					###############				
###################################################################################################################

###################################################################################################################
/*	
	
	TO BE REMEMBERED

	FOR cloudname52, cloudname53 BHCLOC53, AND BHCLOC54 $serviceName="CLCW" 
	FOR ALL OTHER CLOUDS IT WILL BE $serviceName="CLC";

*/
###################################################################################################################


#function to return the list of files sortby date in array
function listdir_by_date($pathtosearch)
{
        foreach (glob($pathtosearch) as $filename)
        {
                $file_array[filectime($filename)]=basename($filename); // or just $filename
        }
        ksort($file_array);
        return $file_array;
}

// to find the hours different of the latest backup file
function hours_diff_of_latest_backup_file($latestFileName)
{
	 $seconds	=	strtotime(date('Y-m-d H:i:s')) - strtotime(date (" Y-m-d H:i:s.", filemtime($latestFileName))) ;
     $hours		=	$seconds / 60 /  60;
	 return $hours;
}

# list of cloud backups which need to be checked
// clouds in espoo location

// clouds in beiging location
$cloudArray	=	array("cloudname53"=>"cloudname53000.china.hostname","cloudname52"=>"cloudname72.china.hostname");


$to	=	'samsudheen.saludheen.ext@gmail.com';

foreach($cloudArray as $key => $val)
{
        $cloudName	=	$key;
        $hostName	=	$val;
		if($key=='cloudname52' || $key=='bhcloc53')
			$serviceName	=	"CLCW";
		else
			$serviceName	=	"CLC";
        $directoryName	=	"/opt/cloudbackup/".$cloudName."/";
        
		
		#########################################################################
        ##########  CHECKING LOG BACKUPS IN ALL CLOUD
        #########################################################################
        
		$fileName	=	$cloudName."_".$hostName."_".$serviceName."_logs_*.tar.gz";
        $sorted_array	=	listdir_by_date($directoryName.$fileName);
        //$lastarray=end($sorted_array);
        $latestFileName	=	$directoryName.end($sorted_array);#file name which copied recently#'ssmas.pem';
        echo "\n".$latestFileName;
         
		$hours	=	hours_diff_of_latest_backup_file($latestFileName);
        echo "\n".$hours;

        # if the last backup was taken more than 9 hours then trigger the mail with exact date time when the back was taken
        if($hours > 9)
        {
                $subject = $cloudName.' Log Backup failed - Last backup was taken at'.date (" Y-m-d H:i:s.", filemtime($latestFileName));
                $message = $cloudName.' Log Backup failed - Last backup was taken at'.date (" Y-m-d H:i:s.", filemtime($latestFileName)).'('.round($hours,2).' hours ago )';
                mail($to, $subject, $message);

        }
        



        #########################################################################
        ##########  CHECKING SQL BACKUPS IN ALL CLOUD
        #########################################################################
       
		$sqlBackUpFileName      =   $cloudName.'_dbdumpall_*.sql.gz';
        $sqlFileSortedArray		=	listdir_by_date($directoryName.$sqlBackUpFileName);
        
        $latestSqlFileName		=	$directoryName.end($sqlFileSortedArray);#file name which copied recently#'
        echo "\n".$latestSqlFileName; //escloc13_dbdumpall_2014111521.sql.gz
         
		$hoursOfSqlFileLastCreated = hours_diff_of_latest_backup_file($latestSqlFileName);
        echo "\n".$hoursOfSqlFileLastCreated;

        # if the last backup was taken more than 9 hours then trigger the mail with exact date time when the back was taken
        if($hoursOfSqlFileLastCreated > 9)
        {
                $subjectSQL = $cloudName.' SQL Backup failed - Last sql backup was taken at'.date (" Y-m-d H:i:s.", filemtime($latestSqlFileName));
                $messageSQL = $cloudName.' SQL Backup failed - Last sql backup was taken at'.date (" Y-m-d H:i:s.", filemtime($latestSqlFileName)).'('.round($hoursOfSqlFileLastCreated,2).' hours ago )';
                mail($to, $subjectSQL, $messageSQL);

        }
        
		
		#########################################################################
        ##########  CHECKING REPORTING_DB BACKUPS IN ALL CLOUD
        #########################################################################
        
		//escloc13_reporting_data_2014120400.dat
        $reportdbBackUpFileName	=	$cloudName.'_reporting_data_*.dat';
        $reportdbFileSortedArray	=	listdir_by_date($directoryName.$reportdbBackUpFileName);
        
        $latestReportdbFileName	=	$directoryName.end($reportdbFileSortedArray);#file name which copied recently#'
        echo $latestReportdbFileName;  //escloc13_dbdumpall_2014111521.sql.gz
         
		$hoursOfReportdbFileLastCreated	=	hours_diff_of_latest_backup_file($latestReportdbFileName);
        echo "\n".$hoursOfReportdbFileLastCreated;
        # if the last backup was taken more than 24 hours then trigger the mail with exact date time when the back was taken
        if($hoursOfReportdbFileLastCreated > 24)
        {
                
                $subjectRDB = $cloudName.' ReportDB Backup failed - Last ReportDB backup was taken at'.date (" Y-m-d H:i:s.", filemtime($latestReportdbFileName));
                $messageRDB = $cloudName.' ReportDB Backup failed - Last ReportDB backup was taken at'.date (" Y-m-d H:i:s.", filemtime($latestReportdbFileName)).'('.round($hoursOfReportdbFileLastCreated,2).' hours ago )';
                mail($to, $subjectRDB, $messageRDB);

        }
}
mail('samsudheen.saludheen.ext@gmail.com','backup check script executed in esclos64','backup check script executed in esclos64');
?>
