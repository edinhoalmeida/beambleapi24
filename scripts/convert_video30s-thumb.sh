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

ffmpeg \
        -ss 00:00:01.00 \
        -i ${FILE2} \   
        -vf 'scale=720:1280:force_original_aspect_ratio=decrease' \ 
        -vframes 1 ${THUMB}

