. /root/.euca/eucarc


echo "instancearray=(" > instarray.sh
euca-describe-instances verbose | grep running | awk {'print $2'} >> instarray.sh
echo ")" >> instarray.sh

echo "Instance:" > instancesWithOutUserId.txt
echo "=========" >> instancesWithOutUserId.txt
. instarray.sh
for i in ${instancearray[@]}
do
        #echo $i
        #sleep 1
        accid=$(euca-describe-instances verbose $i| awk 'NR==1' | awk {'print $3'})
        accname=$(euare-accountlist | grep $accid | awk {'print $1'})
        #echo $accname
        if [ -z $accname ]
        then
           echo $i >> instancesWithOutUserId.txt
        fi
done
echo "Instances with out userid is captured in instancesWithOutUserId.txt"
