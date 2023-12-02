#!/usr/bin/env bash

VER=${1-0.1.1}
DIST_DIR=${2-./dist}
DIR_TO_ZIP=wp-job-board
ARCHIVE=wp-job-board.$VER.zip

if [ -f $DIST_DIR/$ARCHIVE ]; then
    rm $DIST_DIR/$ARCHIVE;
fi

mkdir -p $DIST_DIR/$DIR_TO_ZIP

cp -af admin $DIST_DIR/$DIR_TO_ZIP
cp -af includes $DIST_DIR/$DIR_TO_ZIP
cp -af languages $DIST_DIR/$DIR_TO_ZIP
cp -af public $DIST_DIR/$DIR_TO_ZIP
cp wp-job-board.php $DIST_DIR/$DIR_TO_ZIP
cp uninstall.php $DIST_DIR/$DIR_TO_ZIP
cp index.php $DIST_DIR/$DIR_TO_ZIP
cp readme.txt $DIST_DIR/$DIR_TO_ZIP

cd $DIST_DIR && zip -r $ARCHIVE  $DIR_TO_ZIP

rm -rf $DIR_TO_ZIP
