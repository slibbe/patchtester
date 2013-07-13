#!/bin/sh
rm -rf ../packaging && mkdir ../packaging
rm -rf ../packages && mkdir ../packages
cp -r ../../administrator/components/com_patchtester ../packaging/admin
rm -rf ../packaging/admin/backups/*.txt
mv ../packaging/admin/patchtester.xml ../packaging/patchtester.xml
tar jcf ../packages/com_patchtester.tar.bz2 ../packaging/admin/ ../packaging/patchtester.xml
tar zcf ../packages/com_patchtester.tar.gz ../packaging/admin ../packaging/patchtester.xml
zip -r ../packages/com_patchtester.zip ../packaging/admin/ ../packaging/patchtester.xml
