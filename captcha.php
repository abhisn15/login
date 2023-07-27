<?php
session_start();

// Function to generate a random string of characters (A-Z and 0-9)
function generateRandomString($length = 4) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    // Initialize counters for characters and digits
    $charCount = 0;
    $digitCount = 0;

    for ($i = 0; $i < $length; $i++) {
        // Randomly decide whether to add a character or a digit
        $isCharacter = (rand(0, 1) == 1);

        if ($isCharacter && $charCount < ($length / 2)) {
            // Add a character (A-Z)
            $randomString .= $characters[rand(0, 25)];
            $charCount++;
        } elseif (!$isCharacter && $digitCount < ($length / 2)) {
            // Add a digit (0-9)
            $randomString .= $characters[rand(26, $charactersLength - 1)];
            $digitCount++;
        } else {
            // If adding more characters or digits exceeds half of the length,
            // force to add the remaining type (character or digit).
            if ($charCount < ($length / 2)) {
                $randomString .= $characters[rand(0, 25)];
                $charCount++;
            } else {
                $randomString .= $characters[rand(26, $charactersLength - 1)];
                $digitCount++;
            }
        }
    }

    return $randomString;
}


// Function to create captcha image
function createCaptchaImage($captcha) {
    $width = 200;
    $height = 100;
    $image = imagecreatetruecolor($width, $height);

    // Define colors
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    $lineColor = imagecolorallocate($image, 0, 0, 0);
    $noiseColor = imagecolorallocate($image, 150, 150, 150);

    // Fill the background
    imagefill($image, 0, 0, $backgroundColor);

    // Draw characters
    $characters = str_split($captcha);
    $charCount = count($characters);
    $angleRange = 20; // Maximum angle range for rotation
    $angle = rand(-$angleRange, $angleRange); // Random angle of rotation

    // Calculate y-coordinate positions for each character
    $yPositions = array();
    for ($i = 0; $i < $charCount; $i++) {
        do {
            $yPositions[$i] = rand(30, 60); // Random y-position for each character
        } while ($i > 0 && abs($yPositions[$i] - $yPositions[$i - 1]) < 15); // Ensure characters are not on the same row
    }

    $areAllOnSameRow = true;
    for ($i = 1; $i < $charCount; $i++) {
        if ($yPositions[$i] !== $yPositions[0]) {
            $areAllOnSameRow = false;
            break;
        }
    }

    

    // Draw each character
    for ($i = 0; $i < $charCount; $i++) {
        $char = $characters[$i];
        $yPosition = $yPositions[$i];
        imagettftext($image, 30, $angle, 20 + $i * 40, $yPosition, $textColor, './rawr.ttf', $char);

        // Check if the character is already on the same row as another character
        for ($j = 0; $j < $i; $j++) {
            if ($yPositions[$j] == $yPosition) {
                // If so, re-calculate the y-position
                do {
                    $yPosition = rand(30, 60);
                } while ($yPositions[$j] == $yPosition);
            }
        }
    }

    // Draw lines
    for ($i = 0; $i < 40; $i++) {
        imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
    }

    // Draw noise points
    for ($i = 0; $i < 3; $i++) {
        imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
    }

    // Save the image as a file
    $captchaFileName = 'captcha_' . time() . '.png';
    imagepng($image, $captchaFileName);
    imagedestroy($image);

    return $captchaFileName;
}


// Generate random captcha string
$captchaString = generateRandomString();

// Store the captcha string in a session for verification later
$_SESSION['captcha'] = $captchaString;

// Create the captcha image and get the filename
$captchaImageFile = createCaptchaImage($captchaString);

// Display the captcha image
header('Content-Type: image/png');
readfile($captchaImageFile);

// Remove the captcha image file
unlink($captchaImageFile);
