<?php
// Create image with EXIF data for Challenge 1

// Create simple image
$img = imagecreatetruecolor(800, 600);
$bg = imagecolorallocate($img, 26, 14, 39);
$text_color = imagecolorallocate($img, 0, 255, 136);

imagefill($img, 0, 0, $bg);

// Add text
$text = "OSINT Challenge #1\nFind the hidden data!";
imagestring($img, 5, 250, 280, "OSINT Challenge", $text_color);
imagestring($img, 3, 280, 320, "Find the metadata!", $text_color);

// Save temporary
$temp_file = 'challenges/osint/assets/temp.jpg';
imagejpeg($img, $temp_file, 90);
imagedestroy($img);

// Add EXIF data using exiftool (jika tidak ada, skip)
// Atau manual edit EXIF via online tool

echo "Image created: challenges/osint/assets/temp.jpg<br>";
echo "Now manually add EXIF comment: FLAG{3x1f_d4t4_l34k}<br>";
echo "Use: https://www.thexifer.net/image.php<br>";
echo "Then rename to challenge1.jpg";
?>