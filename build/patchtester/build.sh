#!/bin/sh
rm -rf ../packaging && mkdir ../packaging
rm -rf ../packages && mkdir ../packages
cp -r ../../administrator/components/com_patchtester ../packaging/admin
cp -r ../../components/com_patchtester ../packaging/site
rm -rf ../packaging/admin/backups/*.txt
mv ../packaging/admin/patchtester.xml ../packaging/patchtester.xml
tar jcf ../packages/com_patchtester.tar.bz2 ../packaging/site/ ../packaging/admin/ ../packaging/patchtester.xml
tar zcf ../packages/com_patchtester.tar.gz ../packaging/site/ ../packaging/admin ../packaging/patchtester.xml
zip -r ../packages/com_patchtester.zip ../packaging/ite/ ../packaging/admin/ ../packaging/patchtester.xml
