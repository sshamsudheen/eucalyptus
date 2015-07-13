#/bin/bash
outputFileName='/tmp/inssshop.txt'
touch $outputFileName
instance_array_name=$(euca-describe-instances | grep INSTANCE | awk {'print $4'})

for i in ${instance_array_name[@]}; do

        echo $i
	#sshOP=$(ssh -oBatchMode=yes -l root $i  -oStrictHostKeyChecking=no) 
	result=$(ssh -oBatchMode=yes -l root $i  -oStrictHostKeyChecking=no 2>&1)
        reqsubstr="Permission denied"        
	if [[ "${result}" == *$reqsubstr* ]]; then
                echo "instance $i is reachable";
        else
		echo "cat able to ssh $i" >> $outputFileName
		echo "==================" >> $outputFileName
                echo "cat able to ssh $i";
		#echo "String '$sshop' don't contain substring: '$reqsubstr'."
        fi
done


