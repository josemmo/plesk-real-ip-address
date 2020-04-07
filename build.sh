#!/bin/bash
SCRIPT=`realpath $0`
SCRIPT_PATH=`dirname $SCRIPT`
TARGET="$SCRIPT_PATH/extension.zip"

rm -f "$TARGET"
cd "$SCRIPT_PATH/src" && zip -r4 "$TARGET" * >/dev/null
echo "Saved build to $TARGET"
