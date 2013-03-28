#!/bin/sh
find  ../code/newsletter -type f  | grep php | grep -v svn | grep -v vendor > files.txt
find  ../design/scripts -type f | grep nl_ | grep php | grep -v svn >> files.txt
xgettext -f files.txt --default-domain=newsletter --output-dir=./po --language=PHP --no-wrap --omit-header --no-location --sort-output
# alleen wanneer nieuw (dus nog geen bestand bestaat):
# msginit --locale=en --input=./po/newsletter.po --output=./po/newsletter_en_GB.po --no-wrap --no-translator
msgmerge ./po/newsletter_en_GB.po ./po/newsletter.po --output-file=./po/newsletter_en_GB.po --no-wrap --no-location -v
msgmerge ./po/newsletter_nl_NL.po ./po/newsletter.po --output-file=./po/newsletter_nl_NL.po --no-wrap --no-location -v
msgmerge ./po/newsletter_fr_FR.po ./po/newsletter.po --output-file=./po/newsletter_fr_FR.po --no-wrap --no-location -v
msgmerge ./po/newsletter_de_DE.po ./po/newsletter.po --output-file=./po/newsletter_de_DE.po --no-wrap --no-location -v
rm files.txt
