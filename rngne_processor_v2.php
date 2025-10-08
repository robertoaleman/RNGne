<?php
/**
 * The RNGne Processor v2.0 is an enhanced True Random Number Generator (TRNG) that extracts entropy from physical phenomena captured in images. The major upgrade is the integration of advanced Rényi Entropy analysis, moving the tool from simple statistical testing to robust cryptographic auditing.
  Author: Roberto Aleman
  Documentation: https://ventics.com/rngne-processor-v2-0/
 **/

class RNGneProcessor
{
    /**
     * Calculates the Shannon Entropy (H1, in bits) of a binary data sequence.
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

    // ----------------------------------------------------------------------
    // NEW METHODS FOR RÉNYI ENTROPY (Min-Entropy & Collision Entropy)
    // ----------------------------------------------------------------------

    /**
     * Calculates the probabilities of data blocks, necessary for Rényi entropy metrics.
     * Operates on blocks of 8 bits (1 byte) by default for cryptographic analysis.
     * @param string $data The bit sequence.
     * @param int $blockSize The size of the blocks (e.g., 8 for bytes).
     * @return array<float> A list of the probabilities p_i.
     */
    private function calculateBlockProbabilities(string $data, int $blockSize = 8): array
    {
        $length = strlen($data);
        if ($length < $blockSize) {
            return [];
        }

        // Trim the data so it's perfectly divisible by the block size
        $data = substr($data, 0, $length - ($length % $blockSize));
        $length = strlen($data);

        $blocks = [];
        for ($i = 0; $i < $length; $i += $blockSize) {
            $blocks[] = substr($data, $i, $blockSize);
        }

        $counts = array_count_values($blocks);
        $totalBlocks = count($blocks);

        return array_map(fn($count) => $count / $totalBlocks, $counts);
    }

    /**
     * Calculates the Min-Entropy (H_inf) of a binary data sequence using a block analysis.
     * H_inf = -log2(max(p_i)). This is the most crucial metric for cryptographic security.
     * @param string $data The bit sequence.
     * @param int $blockSize The block size to analyze (default 8 bits).
     * @return float The calculated Min-Entropy.
     */
    public function calculateMinEntropy(string $data, int $blockSize = 8): float
    {
        $probabilities = $this->calculateBlockProbabilities($data, $blockSize);

        if (empty($probabilities)) {
            return 0.0;
        }

        $maxProb = max($probabilities);

        // H_inf = -log2(max(p))
        return ($maxProb > 0) ? (-log($maxProb) / log(2)) : 0.0;
    }

    /**
     * Calculates the Collision Entropy (H2) of a binary data sequence using a block analysis.
     * H2 = -log2(sum(p_i^2)). This measures how far the distribution is from uniform.
     * @param string $data The bit sequence.
     * @param int $blockSize The block size to analyze (default 8 bits).
     * @return float The calculated Collision Entropy.
     */
    public function calculateCollisionEntropy(string $data, int $blockSize = 8): float
    {
        $probabilities = $this->calculateBlockProbabilities($data, $blockSize);

        if (empty($probabilities)) {
            return 0.0;
        }

        $collisionSum = array_reduce($probabilities, fn($sum, $p) => $sum + ($p * $p), 0.0);

        // H2 = -log2(sum(p^2))
        return ($collisionSum > 0) ? (-log($collisionSum) / log(2)) : 0.0;
    }

    // ----------------------------------------------------------------------
    // ORIGINAL METHODS (Image Processing and Extractor)
    // ----------------------------------------------------------------------

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