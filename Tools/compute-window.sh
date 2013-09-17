#!/bin/bash

for i in {1..20}; do
  python s2-compute-accuracy.py s2.data.final.json s1_c.v3.truth.json ${i} > data/s2.window${i}.c.accuracy.log
  echo s2.data.final.json window $i cooking >> s2.accuracy.log
  cat data/s2.window${i}.c.accuracy.log | grep 'GLOBAL' >> s2.accuracy.log
done

for i in {1..20}; do
  python s2-compute-accuracy.py s2.data.final.json s1_m.v3.truth.json ${i} > data/s2.window${i}.m.accuracy.log
  echo s2.data.final.json window $i makeup >> s2.accuracy.log
  cat data/s2.window${i}.m.accuracy.log | grep 'GLOBAL' >> s2.accuracy.log
done

for i in {1..20}; do
  python s2-compute-accuracy.py s2.data.final.json s1_p.v3.truth.json ${i} > data/s2.window${i}.p.accuracy.log
  echo s2.data.final.json window $i photoshop >> s2.accuracy.log
  cat data/s2.window${i}.p.accuracy.log | grep 'GLOBAL' >> s2.accuracy.log
done