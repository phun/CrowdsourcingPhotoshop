#!/bin/bash

regex="^((http[s]?|ftp):\/)?\/?([^:\/\s]+)((\/\w+)*\/)([\w\-\.]+[^#?\s]+)(.*)?(#[\w\-]+)?$"

n=0
while read line; do
	n=$(($n+1));
	echo $n $line;
	path=$( echo "$line" | perl -MURI -le 'chomp($line = <>); print URI->new($line)->path' )
	# echo $path
	arr=$(echo $path | tr "/" "\n")
	i=1
	for x in $arr; do
		if [[ $i -eq 2 ]]; then
	    	# echo "$x"
	    	vid=$x
	    fi
	    if [[ $i -eq 4 ]]; then
	    	# echo "$x"
	    	filename=$x
	    fi
	    let i++
	done
	echo $vid/$filename
	# AFTER_SLASH=${path##*/}
	# echo $AFTER_SLASH
	# if [[ $line =~ $regex ]]; then
	# 	echo "matches"
	# 	i=1
 #        match=${#BASH_REMATCH[*]}
 #        while [[ $i -lt $match ]]
 #        do
 #            echo "  capture[$i]: ${BASH_REMATCH[$i]}"
 #            let i++
 #        done
 #    fi
	#wget $line --no-check-certificate -O $path
	curl $line --create-dir -o $vid/$filename
done <"url-storyboard-list"
echo "Final line count is: $n";