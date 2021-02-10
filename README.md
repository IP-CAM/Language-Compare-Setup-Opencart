# Opencart Language Compare
Compares CMS opencart language files and generates one mixed file


## When to use:
If you need to add a language pack to your existing online store on Opencart.
Compares language arrays in files and based on them creates a language pack, with partially translated strings.
The rest of the lines can be translated manually, thereby saving your time on adding the translation to the site

### How to use
The script was written and tested on php 7.1.2.

To run you need:
1) Place the original language pack in the original folder (the one used on the site)
2) Place the translation in the translate folder.
3) Open a console and run the following command,
4) After the script is executed, the new files will be located in the result folder

> php brain.php TRANSLATION_Language_Code SOURCE_Language_Code 
