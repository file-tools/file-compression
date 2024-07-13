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
function updateFileCreationDateSet($filePath, $newCreationDate)
{
    // Parse the input date string and convert it to a timestamp
    $timestamp = strtotime($newCreationDate);

    // Check if the timestamp is valid (not equal to false)
    if ($timestamp === false) {
        // Date is not properly formatted
        $result = "❌ Inputted time string not properly formatted. ";
    } else {
        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);

        // Format the date and time components
        $formattedDate = $dateTime->format('m/d/y H:i:s'); // Format as 'YYYY-MM-DD HH:MM:00'

		// Escape the shell arguments
		$formattedDate = escapeshellarg($formattedDate);
		$filePath = escapeshellarg($filePath);

		$command = "SetFile -d $formattedDate -m $formattedDate $filePath";

        $output = shell_exec($command);

        // Check if the command was successful
        if ($output === null) {
            $result = "✅ Success! Creation and modification updated to {$formattedDate}"; // Green check emoji and formatted date
        } else {
            $result = "❌ Failed to update creation and modification date.";
        }
    }

    return $result;
}


function updateFileCreationDate($filePath, $newCreationDate)
{
    // Parse the input date string and convert it to a timestamp
    $timestamp = strtotime($newCreationDate);

    // Check if the timestamp is valid (not equal to false)
    if ($timestamp === false) {
        // Date is not properly formatted
        $result = "❌ Inputted time string not properly formatted.";
    } else {
        // Set the modification time of the file
        if (touch($filePath, $timestamp)) {
            // Success
            $formattedDate = date('m/d/y H:i:s', $timestamp); // Format as 'MM/DD/YY HH:MM:SS'
            $result = "✅ Success! Modification time updated to {$formattedDate}";

            // On macOS, directly modifying creation time is not supported.
            // We can only update the modification time using touch.
            // If you need to change the creation time, you may need to use more advanced file system manipulation techniques.
        } else {
            // Failed to update modification time
            $result = "❌ Failed to update modification time.";
        }
    }

    return $result;
}



// Function to run PNGQuant on an image file with customizable arguments
function runPngQuant($inputPath, $arguments = '')
{
    $inputPath = '"' . $inputPath . '"';
    $command = "pngquant $arguments $inputPath";
    exec($command);
    echo "Processed: $inputPath\n";
}



// Function to process PNG files in a directory
function processPNGFiles($directory)
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $allowedExtensions = ['png'];
            $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
            
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                // Get the original creation timestamp of the file

                  // Normalize and sanitize the file path
                $normalizedPath = normalizeFilePath($file->getPathname());

                $originalCreationTimestamp = getCreationTimestamp($normalizedPath);

                $formattedDate = date('Y-m-d H:i:s', $originalCreationTimestamp);

                // $originalCreationTimestamp = 1439095112;

                echo "Original Creation Timestamp: $formattedDate\n";
                
                // Run PNGQuant on the PNG image with customizable arguments
                runPngQuant($normalizedPath, '--quality=60-80 --skip-if-larger --ext=.png --force');
                
                // Update the creation date of the file
                echo updateFileCreationDate($normalizedPath, $formattedDate);


                echo "Original Creation Timestamp: $formattedDate\n";
                echo "------\n------\n";


            }
        }
    }
}

include("config.php");

$directory = normalizeFilePath($inputDirectory);

// Process PNG files and update creation dates using the functions from PngQuant.php
processPNGFiles($directory);

echo "PNGQuant processing and creation date update completed.\n";

