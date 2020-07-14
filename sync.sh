#!/bin/sh

rsync --progress -rvht --delete-after root@194.67.78.176:/var/www/pbx.smart-php.design/* ./
rsync --progress -rvht --delete-after root@194.67.78.176:/var/www/pbx.smart-php.design/.[^.]* ./
rsync --progress -rvht --delete-after root@194.67.78.176:/var/www/pbx.smart-php.design/.git/* ./.git/