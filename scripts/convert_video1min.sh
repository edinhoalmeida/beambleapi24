#!/bin/bash
while getopts n:b:d:t: flag

do
        case "${flag}" in
                n) name=${OPTARG}
                         ;;
                d) destiny=${OPTARG}
                         ;;
                t) thumb=${OPTARG}
                         ;;
                b) FOLDER=${OPTARG}
                         ;;
                *) echo "Invalid option: -$flag" ;;
        esac
done


FILE="$FOLDER/$name"
FILE2="$FOLDER/$destiny"
THUMB="$FOLDER/$thumb"


# ffmpeg -y -i beamble_tests1.mp4 -ss 00:00:00.000 -to 00:01:00.000 -vf scale=1080:1920 -c:v libx264 -b:v 4M -maxrate 4M -bufsize 8M -preset fast -profile:v high -level 4.1 -c:a aac -b:a 128k -movflags +faststart -f mp4 beamble_tests1_optimized.mp4
# ffmpeg -y -i beamble_tests2.mp4 -ss 00:00:00.000 -to 00:01:00.000 -vf scale=1080:1920 -c:v libx264 -b:v 4M -maxrate 4M -bufsize 8M -preset fast -profile:v high -level 4.1 -c:a aac -b:a 128k -movflags +faststart -f mp4 beamble_tests2_optimized.mp4
        

# ffmpeg -y -ss 00:00:02 -i beamble_tests1_optimized.mp4 -vframes 1 -update true beamble_tests1_thumb.jpg
# ffmpeg -y -ss 00:00:02 -i beamble_tests2_optimized.mp4 -vframes 1 -update true beamble_tests2_thumb.jpg

ffmpeg -y -i ${FILE} -ss 00:00:00.000 -to 00:01:00.000 -vf scale=1080:1920 -c:v libx264 -b:v 4M -maxrate 4M -bufsize 8M -preset fast -profile:v high -level 4.1 -c:a aac -b:a 128k -movflags +faststart -f mp4 ${FILE2}        

ffmpeg -y -ss 00:00:02 -i ${FILE2} -vframes 1 -update true ${THUMB}
