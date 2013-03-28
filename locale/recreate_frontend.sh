#!/bin/sh
find  ../site/design -type f  | grep php | grep -v svn > files.txt
xgettext -f files.txt --default-domain=frontend --output-dir=./po --language=PHP --from-code=utf-8 --no-wrap --no-location --sort-output
# alleen wanneer nieuw (dus nog geen bestand bestaat):
# msginit --locale=en --input=./po/frontend.po --output=./po/frontend_en_GB.po --no-wrap --no-translator
msgmerge ./po/frontend_en_GB.po ./po/frontend.po --output-file=./po/frontend_en_GB.po --no-wrap --no-location -v
msgmerge ./po/frontend_nl_NL.po ./po/frontend.po --output-file=./po/frontend_nl_NL.po --no-wrap --no-location -v
msgmerge ./po/frontend_fr_FR.po ./po/frontend.po --output-file=./po/frontend_fr_FR.po --no-wrap --no-location -v
msgmerge ./po/frontend_de_DE.po ./po/frontend.po --output-file=./po/frontend_de_DE.po --no-wrap --no-location -v
rm files.txt
