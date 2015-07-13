#!/bin/bash
# ./createinstances.sh 'emiid' 'keypairname'
#./createinstances.sh emi-11A53303 shams

## soure the user credentials
. /root/.euca/eucarc

## this file has the log information
echo "Log info:" > loginfo.txt

## this file has all the instance id and reservation id
file="./instacesdetails.txt"

for x in {1..2}
do

echo $x
		for i in {1..2}
		do
		   #echo "creating instance no: $i "
		   #echo "r-adadada" >>  instacesdetails.txt
		   #echo "i-asasas" >>  instacesdetails.txt
		   #euca-run-instances $1 -k $2 | awk {'print $2'} >> instacesdetails.txt
		   # euca-run-instances $1 -k $2 -f userdata.txt -t m1.large | awk {'print $2'} >> instacesdetails.txt
		   euca-run-instances $1 -k $2 -g stresstest | awk {'print $2'} >> instacesdetails.txt
		done


		echo "wait for 5 mins to get the status of all the instances"
		sleep 5m
		# fetch all the instance id from instacesdetails.txt and create it in an array
		echo "instancearray=(" > instarray.sh
		sed -n 2~2p instacesdetails.txt >>  instarray.sh
		echo ")" >> instarray.sh


		. instarray.sh
		for i in ${instancearray[@]}
		do
			#echo $i
			euca-describe-instances verbose | grep $i | awk {'print $2 $6'}
			echo "Insstatus=(" > status.sh
			euca-describe-instances verbose | grep $i | awk {'print $6'} >> status.sh
			echo ")" >> status.sh
			. status.sh
			if [ ${Insstatus[0]} != "running" ]
			then
			   echo "not started"
			   echo "==================Instance: "$i"================" >> loginfo.txt
			   cat /var/log/eucalyptus/cloud-output.log | grep $i >> loginfo.txt
			   echo "================== completed ================" >> loginfo.txt
			else
			   echo "started"
			fi
		done
		echo "wait for 5 more mins and then terminate all the instances"
		sleep 5m

		for j in ${instancearray[@]}
		do
			euca-describe-instances verbose | grep $j >> instances_details.txt
			#euca-terminate-instances $j
		done
	
	mv instacesdetails.txt instacesdetails_$x.txt
	mv instarray.sh instarray_$x.sh
	mv status.sh status_$x.sh
done
#rm -rf instacesdetails.txt
#rm -rf instarray.sh
#rm -rf status.sh
echo "=================================="
echo "completed"

