# Screenshot Compression (with Date Preservation!)

## What ⚡
An easy-to-configure wrapper for PNG image compression (using powerful PngQuant algorithm) that preserves file creation dates. Made of MacOS local usage but also configurable for Linux. 

## Why 🤷‍♂️
Date preservation:
- Running many command line tools will reset a file's creation date to date script is run
- I really like preserving this important piece of meta data
- It's especially nice to preseve this if you have years of screenhots you wish to compress but want to remember the year you captured those images

Helpers provide guardrails:
  - Can be hard to remember commands for libraries like PngQuant that don't have a UI
  - Wrappers like this can help by augmenting functionality and with reminders around configuration 

## How 📋

1. First, install dependencies on your Mac. See dependencies below. 
2. Rename config-sample.ini -> config.ini
3. Open config.ini and drag the folder into the open editot to get file path
4. Open Terminal.app and run the "Compress.php" file like  `php /path/to/Compress.php`

You'll now see some status info in your terminal. 

My usage: 
- I have a folder called "Screenshots" in my GoogleDrive and screenshots are set to automatically go there. 
- At the end of each work day I look through what has been saved, delete about 30% of them and then run this script to compress the rest

Important note: 
- This only touches PNG files so you can run it across a mixed fils system and it will only compress PNG

## Dependencies 📦
This script requires installation of PNGQuant on your system:

macOS: `brew install pngquant`
Ubuntu/Debian: `sudo apt-get install pngquant`
Windows: Download from [pngquant.org](https://pngquant.org/)

For MacOS usage, this script also requires Xcode Command Line Tools for the SetFile command:

macOS: `xcode-select --install`

## Additional Compression Tools
I also use a tool called ImageOptim


## Ideas / ToDos
- I could use Alfred or some automatter to improve this
- Plist files can also be set as services that automate this if you keep a dedicated Screenshots folder as well and wish to just compress files righy away, when you capture them. 
- More testing - really only testing this on MacOS but thing it should work on Linux or Ubuntu if time_command is changed
- Perhaps should be renamed from "image-compression" to "image-transformer" given additional of mogrify gives you ability to do much more than just compress an image? 
- 
## Changelog

### 2025-06-03
#### Cleaning up for Github
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



