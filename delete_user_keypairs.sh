#!/bin/bash
# ./delete_user_keypairs.sh <username> - to delete all the keypairs in particular user account
# ./delete_user_keypairs.sh <username> <keypairs by , comma seperated> - to delete all the keypairs except the given keypairs as argument in particular user account

### finction to get the array difference
diff(){
    a1="$1"
    a2="$2"
    awk -va1="$a1" -va2="$a2" '
     BEGIN{
       m= split(a1, A1," ")
       n= split(a2, t," ")
       for(i=1;i<=n;i++) { A2[t[i]] }
       for (i=1;i<=m;i++){
            if( ! (A1[i] in A2)  ){
                printf A1[i]" "
            }
        }
    }'
}
### end of array different function

#get the current datetime to append to the directory name
now="$(date +'%d_%m_%Y_%H_%M_%S')"

#set the directory name to be the account name along with the current date time(to avoid the duplicate folder)
directoryName=$1.$now

#check for the directory name exists, if so tell the user to delete the directory from the server (probably the directory eont exists)
if [ -d "$filename" ]; then
        echo "directory already exists, check it and delete it not necessary"
else
        ## if not create a directory
        mkdir $directoryName
        # download the user credeentials
        euare-userlistbypath --delegate=$1
        euca-get-credentials -e / -u $1 -a $1 $1.zip
        mv $1.zip $directoryName
        cd $directoryName/
        unzip $1.zip
        ## soure the user credentials
        . eucarc
        ## perform the operation
        ## here it deletes the key-pairs for this user
        echo "array_name=(" > keypairs.txt
        euca-describe-keypairs | awk '{print $2}' >> keypairs.txt
        echo ")" >> keypairs.txt
	
	## if the exception keypairs are passed as the argument then delete all the keypairs except the keypairs passed as the argument
        exception_keypairs=$2
        if [ $exception_keypairs  ] ; then
                ## adding more exceptions
                echo "exception added"
                exception_array=$exception_keypairs
                replace_value=" "
                echo "exception_array=(" > exception_array.txt
                echo ${exception_array//,/$replace_value} >> exception_array.txt
                echo ")" >> exception_array.txt
                
                ## include the files , in sh way source the files
                chmod 777 keypairs.txt
                chmod 777 exception_array.txt
                . keypairs.txt
                . exception_array.txt
		# a1 consists of all the keypairs in array
                a1=${array_name[@]}; 
		# a2 consists of keypairs which should not be deleted	
                a2=${exception_array[@]};
		#key_pairs_array consists of array which needs to be deleted
                key_pairs_array=($(diff "${a1}" "${a2}"))                
                
                for (( i = 0 ; i < ${#key_pairs_array[@]} ; i++ )) do
                        echo 'keypair #'.$i.' = '.${key_pairs_array[$i]}
			# delete the keypair
                        ###euca-delete-keypair ${array_name[$i]}
                done

        else
		### if no argument is provided then just delete all the keypairs for the user	
                echo "No exception"
                ## include the file , in sh way source the file
                . keypairs.txt
                for (( i = 0 ; i < ${#array_name[@]} ; i++ )) do
                        echo 'keypair #'.$i.' = '.${array_name[$i]}
                        ###euca-delete-keypair ${array_name[$i]}
                done
                #echo "Not exists"
        fi
        ## end of adding exceptions

fi
