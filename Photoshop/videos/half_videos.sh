for i in {1..10}
do
  ffmpeg -i t1_${i}.mp4 -filter_complex '[0:v]setpts=2.0*PTS[v];[0:a]atempo=0.5[a]' -map '[v]' -map '[a]' t1_${i}_h.mp4
done