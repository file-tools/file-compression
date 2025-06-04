# Screenshot Compression (with Date Preservation!)
 Preserve original creation date when using PNGQuant

## What ⚡
Adds date preservation to the popular PNG QUANT compression algorithm when running on a Mac. 

## Why 🤷‍♂️
Keeping creation dates is important to me and it is annoying that when PNG Quant is run it strips out creation date meta. 

## How 📋
I just have a folder I save screenshots to and then I set the Config file of this script to the path of that folder and then I manually run this script at the end of each day to compress the files I've save as I review them and see which I need and which I can delete. 
IMPORTANT NOTE:
- This only touches PNG files so you can run it across a mixed fils system and it will only compress PNG

I should use Alfred or some automatter to improve this. 

## Requirements 📦
This script requires installation of PNGQuant on your system:

macOS: `brew install pngquant`
Ubuntu/Debian: `sudo apt-get install pngquant`
Windows: Download from [pngquant.org](https://pngquant.org/)

For MacOS usage, this script also requires Xcode Command Line Tools for the SetFile command:

macOS: `xcode-select --install`

## To Dos 
#### Compatibility
I did this quickly to work for me on my Mac but could be extended to work for more people in various environments. Not sure whether suing Stet or Touch is more universal ... would need to look into this and test. 



## Changelog

### 2025-06-03
#### Claning up for Github
- Move settings to an "ini" file instead of PHP
- Created a config-sample.ini so could gitignore the main
- Made PNGQuant settings a variable in the config so easier to alter, even though I just keep it the same all the time. 
- REMOVED a bash files called Compress.sh that just linked to the php file ... not sure why I had this there. Was like this: 

```
#!/bin/bash
/usr/local/bin/php /Users/Reess/Code/Tools/aCompression/CompressToolRK/Compress.php
```

### 2024-05-02 
#### Fixing date update
Something broke with SetFile command after upgrading to Ventura. The error I got is as follows: 

```
me@mymachine ~ % SetFile 
xcrun: error: invalid active developer path (/Library/Developer/CommandLineTools), missing xcrun at: /Library/Developer/CommandLineTools/usr/bin/xcr
```

Solved by doing the following.
1. Completely remove command line tools using this kind of dangerous method: `sudo rm -rf /Library/Developer/CommandLineTools`
2. Reinstall it - `sudo xcode-select --install`
Other standard ways to uninstall and reinstall did not work. 



