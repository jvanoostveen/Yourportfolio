#!/bin/sh
find  ../code -type f  | grep php | grep -v svn | grep -v newsletter | grep -v vendor > files.txt
find  ../design/html -type f | grep php | grep -v svn >> files.txt
find  ../design/scripts -type f | grep php | grep -v svn | grep -v nl_ >> files.txt
xgettext -f files.txt --default-domain=backend --output-dir=./po --language=PHP --no-wrap --omit-header --no-location --sort-output
# alleen wanneer nieuw (dus nog geen bestand bestaat):
# msginit --locale=en --input=./po/backend.po --output=./po/backend_en_GB.po --no-wrap --no-translator
msgmerge ./po/backend_en_GB.po ./po/backend.po --output-file=./po/backend_en_GB.po --no-wrap --no-location -v
msgmerge ./po/backend_nl_NL.po ./po/backend.po --output-file=./po/backend_nl_NL.po --no-wrap --no-location -v
msgmerge ./po/backend_fr_FR.po ./po/backend.po --output-file=./po/backend_fr_FR.po --no-wrap --no-location -v
msgmerge ./po/backend_de_DE.po ./po/backend.po --output-file=./po/backend_de_DE.po --no-wrap --no-location -v
rm files.txt
