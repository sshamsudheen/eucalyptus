<?php

###################################################################################################################
############						CHECK ALL THE BACKUPS HAPPENING PERIODICALLY					###############				
###################################################################################################################

###################################################################################################################
/*	
	
	TO BE REMEMBERED

	FOR BECLOC52, BECLOC53 BHCLOC53, AND BHCLOC54 $serviceName="CLCW" 
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
/*$cloudArray=array("escloc10"=>"esclos10130.emea.nsn-net.net","escloc11"=>"esclos10200.emea.nsn-net.net",
"escloc12"=>"esclos10271.emea.nsn-net.net","escloc14"=>"esclos14000.emea.nsn-net.net",
"escloc57"=>"esclos50011.emea.nsn-net.net","escloc58"=>"esclos50078.emea.nsn-net.net",
"escloc60"=>"esclos60000.emea.nsn-net.net", "escloc13"=>"esclos13000.emea.nsn-net.net");*/

// clouds in beiging location
$cloudArray	=	array("becloc53"=>"beclos53000.china.nsn-net.net","becloc52"=>"beclos72.china.nsn-net.net");

// clouds in BANGLORE location
//$cloudArray	=	array("bhcloc53"=>"bhclos80.apac.nsn-net.net","bhcloc54"=>"bhclos54000.apac.nsn-net.net");

//$to     =       'shanmugam.ganesan.ext@nsn.com;jagannath.padhi.ext@nsn.com;samsudheen.saludheen.ext@nsn.com;rajesh.sekaran.ext@nsn.com;gunaseelan.ganesan.ext@nsn.com;santhosh.venkatraman.ext@nsn.com;lavanya.mohanasundaram.ext@nsn.com;kalpana.vilvajothy.ext@nsn.com;sudharsan.punniyakotti.ext@nsn.com;tejaashwine.m.ext@nsn.com;dinesh.a.a.ext@nsn.com;muthu_alagappan.muthu.ext@nsn.com;vishnu.priya_s.ext@nsn.com;bharadhwaj.ganesh.ext@nsn.com;yuvarani.parkunan.ext@nsn.com';

$to	=	'samsudheen.saludheen.ext@nsn.com';

foreach($cloudArray as $key => $val)
{
        $cloudName	=	$key;
        $hostName	=	$val;
		if($key=='becloc52' || $key=='bhcloc53')
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
mail('samsudheen.saludheen.ext@nsn.com','backup check script executed in esclos64','backup check script executed in esclos64');
?>
