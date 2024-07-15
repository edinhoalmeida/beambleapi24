#!/bin/bash
while getopts d: flag
do
    case "${flag}" in
        d) FOLDER=${OPTARG}
                    ;;
        *) echo "Invalid option: -$flag" ;;
    esac
done

cd "$FOLDER/"
aws s3 sync . s3://beamble2-s3/media/ --delete
