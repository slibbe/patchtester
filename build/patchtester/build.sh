#!/bin/sh
rm -rf ../packaging && mkdir ../packaging
rm -rf ../packages && mkdir ../packages
cp -r ../../administrator/components/com_patchtester ../packaging/admin
rm -rf ../packaging/admin/backups/*.txt
mv ../packaging/admin/patchtester.xml ../packaging/patchtester.xml
cd ../packaging
tar jcf ../packages/com_patchtester.tar.bz2 admin/ patchtester.xml
tar zcf ../packages/com_patchtester.tar.gz admin patchtester.xml
zip -r ../packages/com_patchtester.zip admin/ patchtester.xml
