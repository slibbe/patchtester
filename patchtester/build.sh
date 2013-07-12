#!/bin/sh
rm -rf site admin patchtester.xml
rm -rf ../com_patchtester.tar.bz2 ../com_patchtester.tar.gz ../com_patchtester.zip
cp -r ../administrator/components/com_patchtester admin
cp -r ../components/com_patchtester site
rm -rf admin/backups/*.txt
mv admin/patchtester.xml patchtester.xml
tar jcf ../com_patchtester.tar.bz2 site admin patchtester.xml
tar zcf ../com_patchtester.tar.gz site admin patchtester.xml
zip -r ../com_patchtester.zip site/ admin/ patchtester.xml
