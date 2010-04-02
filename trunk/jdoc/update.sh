#!/bin/sh

### CHANGE_ME =;)
PATH_HTDOCS="/srv/www/vhosts/der-beta-server.de/subdomains/jdoc/httpdocs"

PATH_SOURCES=$PATH_HTDOCS"/sources/joomla"
PATH_LIBS=$PATH_SOURCES"/trunk/libraries"

PATH_JCLASS_LIST=$PATH_SOURCES"/jclasslist_trunk.php"

echo "Lib Path: $PATH_LIBS"
echo "Updating SVN..."
svn update $PATH_LIBS

echo "Write svn_info to $PATH_HTDOCS""/svn_info"
svn info $PATH_LIBS > $PATH_HTDOCS"/svn_info"

echo "Rename Classlist file"
if [ -f $PATH_JCLASS_LIST ];
then
  mv $PATH_JCLASS_LIST $PATH_JCLASS_LIST".bak"
  echo "Classlist file has been renamed to $PATH_JCLASS_LIST.bak"
else
   echo "Classlist does not exists"
fi

echo "Executing checkdoccomment.php"
php $PATH_HTDOCS"/checkdoccomment.php"

