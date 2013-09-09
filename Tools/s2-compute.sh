#!/bin/bash

for file in s2.data.*.final.json; do
  count=$(($count+1))
  name=${file%.*}
  #echo python s2-compute-accuracy.py ${name##*/} s1_c.truth.json 10
  python s2-compute-accuracy.py $file s1_c.truth.json 10 > ${name}.c.accuracy.log
  python s2-compute-accuracy.py $file s1_m.truth.json 10 > ${name}.m.accuracy.log
  python s2-compute-accuracy.py $file s1_p.truth.json 10 > ${name}.p.accuracy.log
  echo $file cooking >> s2.accuracy.log
  cat ${name}.c.accuracy.log | grep 'Precision' >> s2.accuracy.log
  echo $file makeup >> s2.accuracy.log
  cat ${name}.m.accuracy.log | grep 'Precision' >> s2.accuracy.log
  echo $file photoshop >> s2.accuracy.log
  cat ${name}.p.accuracy.log | grep 'Precision' >> s2.accuracy.log
done
