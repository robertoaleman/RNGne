<?php
/**
 * Class RNGneProcessor ( Random Number Generator from Natural Entropy )
 * Encapsulates the logic for extracting random bits from an image and calculating Shannon Entropy,
 * and uses a cryptographic extractor to ensure a highly strong and secure seed obtained from the entropy of natural phenomena.
 * Author: Roberto Aleman
 * Documentation:https://ventics.com/rngneprocessor/
 */

class RNGneProcessor
{
    /**
     * Calculates the Shannon Entropy (in bits) of a binary data sequence.
     * @param string $data The bit sequence (e.g., "101100101...").
     * @return float The calculated entropy.
     */
    public function calculateShannonEntropy(string $data): float
    {
        $length = strlen($data);
        if ($length === 0) {
            return 0.0;
        }

        // Count the frequency of each symbol ('0' and '1')
        $counts = count_chars($data, 1);
        $entropy = 0.0;

        foreach ($counts as $count) {
            if ($count > 0) {
                $probability = $count / $length;
                // Apply Shannon's formula: H = - sum(p_i * log2(p_i))
                $entropy -= $probability * (log($probability) / log(2));
            }
        }

        return $entropy;
    }

    /**
     * Processes a single image to extract the LSB from the grayscale value of each pixel.
     * @param string $path The path to the image file.
     * @return string The extracted raw bit string.
     * @throws Exception If the image cannot be loaded or the format is unsupported.
     */
    public function processImageForRandomBits(string $path): string
    {
        $allBits = '';
        if (!file_exists($path)) {
            throw new Exception("Error: Image '{$path}' not found.");
        }

        // 1. Load the image
        $imageInfo = getimagesize($path);
        if ($imageInfo === false) {
            throw new Exception("Error: Could not get image information for '{$path}'.");
        }

        $mime = $imageInfo['mime'];
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($path);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($path);
                break;
            default:
                throw new Exception("Error: Image format '{$mime}' not supported.");
        }

        if (!$image) {
            throw new Exception("Error: Could not load image. Ensure GD is enabled.");
        }

        // 2. Get image dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // 3. Iterate over pixels and extract the LSB
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $red = ($rgb >> 16) & 0xFF;
                $green = ($rgb >> 8) & 0xFF;
                $blue = $rgb & 0xFF;

                // Convert to grayscale (simple average) and extract LSB
                $grayValue = floor(($red + $green + $blue) / 3);
                $lsb = $grayValue % 2;
                $allBits .= $lsb;
            }
        }

        imagedestroy($image);
        return $allBits;
    }

    /**
     * Applies a cryptographic hash (SHA-256) to the raw bit string to create a Master Seed.
     * This acts as an Entropy Extractor, guaranteeing uniformity and non-reversibility.
     *
     * @param string $rawBits The long raw bit string extracted from the image.
     * @return string The 256-bit (64-character hexadecimal) Master Cryptographic Seed.
     */
    public function extractMasterSeed(string $rawBits): string
    {
        if (empty($rawBits)) {
            return '';
        }
        // Use SHA-256 as the Entropy Extractor
        return hash('sha256', $rawBits);
    }

}
