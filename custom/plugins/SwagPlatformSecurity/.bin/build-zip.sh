#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd "$DIR/.."

rm -rf SwagPlatformSecurity
mkdir SwagPlatformSecurity
git archive HEAD | tar -x -C SwagPlatformSecurity
zip -r SwagPlatformSecurity.zip SwagPlatformSecurity
rm -rf SwagPlatformSecurity
