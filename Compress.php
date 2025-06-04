<?php
/* 
PNG Quant with Date Preservation
*/

// Function to retrieve the creation timestamp of a file on macOS
function getCreationTimestamp($filePath) {
    $stat = shell_exec("stat -f %B " . escapeshellarg($filePath));
    return (int)$stat;
}

// Function to sanitize and normalize a file path
function normalizeFilePath($path)
{
    // Remove trailing and leading whitespace and quotes
    $path = trim($path, " \t\n\r\0\x0B\"'");

    // Replace backslashes followed by a space with just a space
    $path = preg_replace('/\\\\ +/', ' ', $path);

    // Normalize directory separators to /
    $path = str_replace('\\', '/', $path);

    // Resolve relative paths
    $path = realpath($path);

    return $path;
}


// Function to update the creation and modification date of a file
function updateFileCreationDatetime($filePath, $newCreationDate, $timeCommand = 'SetFile')
{
    // Parse the input date string and convert it to a timestamp
    $timestamp = strtotime($newCreationDate);

    // Check if the timestamp is valid (not equal to false)
    if ($timestamp === false) {
        // Date is not properly formatted
        $result = "❌ Inputted time string not properly formatted.";
    } else {
        if (strtolower($timeCommand) === 'setfile') {
            // Use SetFile command (macOS only)
            $dateTime = new DateTime();
            $dateTime->setTimestamp($timestamp);

            // Format the date and time components
            $formattedDate = $dateTime->format('m/d/y H:i:s');

            // Escape the shell arguments
            $formattedDateEscaped = escapeshellarg($formattedDate);
            $filePathEscaped = escapeshellarg($filePath);

            $command = "SetFile -d $formattedDateEscaped -m $formattedDateEscaped $filePathEscaped";
            $output = shell_exec($command);

            // Check if the command was successful
            if ($output === null) {
                $result = "✅ Success! Creation and modification updated to {$formattedDate}";
            } else {
                $result = "❌ Failed to update creation and modification date.";
            }
        } else {
            // Use touch command (universal)
            if (touch($filePath, $timestamp)) {
                // Success
                $formattedDate = date('m/d/y H:i:s', $timestamp);
                $result = "✅ Success! Modification time updated to {$formattedDate}";

                // Note: On most systems, touch can only update modification time.
                // Creation time modification varies by filesystem and OS.
            } else {
                // Failed to update modification time
                $result = "❌ Failed to update modification time.";
            }
        }
    }

    return $result;
}


// Function to run library command on an image file with customizable arguments
function runLibrarySettings($inputPath, $libCommand)
{
    $inputPath = '"' . $inputPath . '"';
    $command = "$libCommand $inputPath";
    exec($command);
}



// Function to process files in a directory
function processFiles($directory, $libMogrify, $libPngquant, $timeCommand = 'SetFile')
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $allowedExtensions = ['png'];
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                // Normalize and sanitize the file path
                $normalizedPath = normalizeFilePath($file->getPathname());
                
                // Calculate relative path from starting directory
                $relativePath = str_replace($directory . '/', '', $normalizedPath);
                if ($relativePath === $normalizedPath) {
                    // If no directory separator found, try without trailing slash
                    $relativePath = str_replace($directory, '', $normalizedPath);
                    $relativePath = ltrim($relativePath, '/');
                }
                
                echo "---\n";
                echo "Starting $relativePath\n";

                // Get the original creation timestamp of the file
                $originalCreationTimestamp = getCreationTimestamp($normalizedPath);
                $formattedDate = date('Y-m-d H:i:s', $originalCreationTimestamp);
                
                echo "- reading creation date = $formattedDate\n";
                
                // Run mogrify command first if set (for watermarking)
                if (!empty($libMogrify)) {
                    echo "- applying lib_mogrify\n";
                    runLibrarySettings($normalizedPath, $libMogrify);
                }
                
                // Run pngquant command if set (for compression)
                if (!empty($libPngquant)) {
                    echo "- applying lib_pngquant\n";
                    runLibrarySettings($normalizedPath, $libPngquant);
                }
                
                // Update the creation date of the file
                echo "- reapplying creation date\n";
                $result = updateFileCreationDatetime($normalizedPath, $formattedDate, $timeCommand);
                
                // Check if the result indicates success
                if (strpos($result, '✅') !== false) {
                    echo "- ✅ Success!\n";
                } else {
                    echo "- ❌ Error: $result\n";
                }
            }
        }
    }
}

// Read configuration from INI file
$config = parse_ini_file('config.ini');
if ($config === false) {
    die("Error: Unable to parse config.ini file.\n");
}

$inputDirectory = $config['file_directory'] ?? '';
if (empty($inputDirectory)) {
    die("Error: file_directory not specified in config.ini file.\n");
}

$libMogrify = $config['lib_mogrify'] ?? '';
$libPngquant = $config['lib_pngquant'] ?? '';
$timeCommand = $config['time_command'] ?? 'SetFile';

$directory = normalizeFilePath($inputDirectory);

// Process files and update creation dates using the functions
processFiles($directory, $libMogrify, $libPngquant, $timeCommand);

echo "Library processing and creation date update completed.\n";

