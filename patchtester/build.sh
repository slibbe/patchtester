#!/bin/sh
rm -rf site
rm -rf admin
rm -rf ../patchtester.tar.bz2
cp -r ../administrator/components/com_patchtester admin
cp -r ../components/com_patchtester site
rm -rf admin/backups/*.txt
mv admin/patchtester.xml patchtester.xml
tar jcf ../com_patchtester.tar.bz2 site admin patchtester.xml
tar zcf ../com_patchtester.tar.gz site admin patchtester.xml

rm -rf github
mkdir github
cp ../libraries/joomla/client/github.php github
cp ../libraries/joomla/client/githubobject.php github
cp ../libraries/joomla/client/curl.php github
cp -r ../libraries/joomla/client/github github
cp github.xml github
tar jcf ../file_github.tar.bz2 github/*
tar zcf ../file_github.tar.gz github/*

tar jcf ../pkg_patchtester.tar.bz2 pkg_patchtester.xml ../com_patchtester.tar.bz2 ../file_github.tar.bz2
tar zcf ../pkg_patchtester.tar.gz pkg_patchtester_gz.xml ../com_patchtester.tar.gz ../file_github.tar.gz
