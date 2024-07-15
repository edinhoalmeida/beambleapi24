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

CODEC='libx264'



ffmpeg -y -i ${FILE} \
        -ss 00:00:00.000 \
        -to 00:01:00.000 \
        -c:v ${CODEC} \
        -vf "scale=720:1280" \
        -b:v 1600k \
        -c:a aac \
        -rematrix_maxval 1.0 \
        -ac 1 \
        -b:a 64000 \
        -fs 10240000 \
        -f mp4 ${FILE2}

ffmpeg -y -ss 00:00:02 -i ${FILE2} -frames:v 1 ${THUMB}
