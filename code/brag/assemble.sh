#!/bin/bash
# usage: assemble.sh <page.html> <W> <H> <outdir> <poster_t> <final.mp4> <music.wav>
set -e
P=$1; W=$2; H=$3; D=$4; PT=$5; OUT=$6; MUS=$7
node render.js "$P" "$W" "$H" "$D" "$PT" >/dev/null
POS=$(ls "$D"/still_*.jpg | head -1); cp "$POS" "${OUT%.mp4}.jpg"
FIRST=$(node -e "const fs=require('fs');const h=fs.readFileSync('$P','utf8');const m=h.match(/SCENES=\[\{name:'([^']+)'/);console.log(m[1])")
ffmpeg -nostdin -v error -y -i "$D/$FIRST.mp4" -i "$POS" -filter_complex "[1]scale=$W:$H[p];[0][p]overlay=enable='eq(n,0)'" -c:v libx264 -preset veryfast -crf 18 -pix_fmt yuv420p -r 30 "$D/tmp_first.mp4"
mv "$D/tmp_first.mp4" "$D/${FIRST}_p.mp4"
LIST="$D/list.txt"; : > "$LIST"
for s in $(node -e "const fs=require('fs');const h=fs.readFileSync('$P','utf8');const m=[...h.matchAll(/\{name:'([^']+)',start/g)].map(x=>x[1]);console.log(m.join(' '))"); do
  if [ "$s" = "$FIRST" ]; then echo "file '${FIRST}_p.mp4'" >> "$LIST"; else echo "file '$s.mp4'" >> "$LIST"; fi
done
ffmpeg -nostdin -v error -y -f concat -safe 0 -i "$LIST" -c copy "$D/video_only.mp4"
ffmpeg -nostdin -v error -y -i "$D/video_only.mp4" -i "$MUS" -map 0:v -map 1:a -c:v copy -af loudnorm=I=-14:TP=-1.5:LRA=11 -ar 48000 -c:a aac -b:a 192k -shortest -movflags +faststart "${OUT%.mp4}.tmp.mp4"
mv "${OUT%.mp4}.tmp.mp4" "$OUT"
ffprobe -v error -count_frames -select_streams v -show_entries stream=nb_read_frames,width,height,r_frame_rate -show_entries format=duration -of compact "$OUT"
