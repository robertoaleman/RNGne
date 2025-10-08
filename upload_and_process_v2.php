<?php
/**
 * The RNGne Processor v2.0 is an enhanced True Random Number Generator (TRNG) that extracts entropy from physical phenomena captured in images. The major upgrade is the integration of advanced Rényi Entropy analysis, moving the tool from simple statistical testing to robust cryptographic auditing.
  Author: Roberto Aleman
  Documentation: https://ventics.com/rngne-processor-v2-0/
 **/
require_once 'rngne_processor_v2.php';

$output = '';
$fileLink = '';
$results = [];
define('UPLOAD_DIR', 'uploads/');

// --- Configuration ---
$blockSize = 8; // Analyze in 8-bit blocks (one byte)

// --- Processing Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['entropy_file'])) {
    $file = $_FILES['entropy_file'];

    // 1. Setup upload directory
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }

    // Use a unique name for the uploaded image
    $tempFileName = uniqid('img_') . '_' . basename($file['name']);
    $uploadFilePath = UPLOAD_DIR . $tempFileName;

    try {
        // Basic file validation
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading file: Code {$file['error']}");
        }

        // 2. Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadFilePath)) {
            throw new Exception("Failed to move uploaded file.");
        }

        // 3. Initialize and call the RNGneProcessor
        $processor = new RNGneProcessor();
        $rawRandomBits = $processor->processImageForRandomBits($uploadFilePath);

        $bitLength = strlen($rawRandomBits);

        if ($bitLength === 0) {
            throw new Exception("Extraction failed: Zero bits were extracted.");
        }

        // 4. Calculate ALL Entropies (Shannon on single bits; Rényi on blocks)
        $shannonEntropy = $processor->calculateShannonEntropy($rawRandomBits);
        $minEntropy = $processor->calculateMinEntropy($rawRandomBits, $blockSize);
        $collisionEntropy = $processor->calculateCollisionEntropy($rawRandomBits, $blockSize);

        // 5. Generate Master Seed (The Entropy Extractor Phase)
        $masterSeed = $processor->extractMasterSeed($rawRandomBits);

        // 6. Save the raw bit string to a TXT file
        $outputFileName = 'RNGne_' . pathinfo($tempFileName, PATHINFO_FILENAME) . '.txt';
        $outputFilePath = UPLOAD_DIR . $outputFileName;
        file_put_contents($outputFilePath, $rawRandomBits);

        // 7. Prepare results for display
        $results = [
            'original_file' => $file['name'],
            'bit_length' => number_format($bitLength) . ' bits',
            'shannon_entropy' => number_format($shannonEntropy, 4),
            'min_entropy' => number_format($minEntropy, 4),
            'collision_entropy' => number_format($collisionEntropy, 4),
            'block_size' => $blockSize,
            'master_seed' => $masterSeed,
            'output_file_name' => $outputFileName,
        ];

        $fileLink = UPLOAD_DIR . $outputFileName;

    } catch (Exception $e) {
        $output = "<div class='error'>Error: " . $e->getMessage() . "</div>";
        // Attempt to clean up the partially uploaded file
        if (isset($uploadFilePath) && file_exists($uploadFilePath)) {
            unlink($uploadFilePath);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RNGne Processor v2.0 - Natural Entropy Generator</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #2c3e50; padding-bottom: 5px; }
        h1 { border-bottom: 2px solid #bdc3c7; padding-bottom: 10px; }
        form { margin-top: 20px; padding: 20px; border: 1px dashed #3498db; border-radius: 4px; background-color: #ecf0f1; }
        input[type="file"], input[type="submit"] { padding: 10px; margin: 5px 0; border-radius: 4px; border: 1px solid #ddd; }
        input[type="submit"] { background-color: #2ecc71; color: white; cursor: pointer; border: none; font-weight: bold; }
        input[type="submit"]:hover { background-color: #27ae60; }
        .results { margin-top: 30px; padding: 20px; background-color: #e8f5e9; border: 1px solid #4CAF50; border-radius: 8px; }
        .results p { margin: 8px 0; }
        .entropy-value { font-size: 1.5em; font-weight: bold; }
        .shannon-h1 { color: #3498db; }
        .collision-h2 { color: #f39c12; }
        .min-hinf { color: #c0392b; }
        .seed-box { background-color: #34495e; color: #fff; padding: 15px; border-radius: 4px; overflow-wrap: break-word; font-family: 'Courier New', monospace; font-size: 0.9em; margin-top: 10px; }
        .error { color: #c0392b; background-color: #fdedee; padding: 10px; border: 1px solid #f9c0c0; border-radius: 4px; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>RNGne Processor v2.0 - Natural Entropy Extractor </h1><p> Author: Roberto Aleman<br>
        <a href="https://ventics.com/rngne-processor-v2-0/">Documentation</a></p>


        <p>Upload a high-entropy image (JPEG, PNG, or GIF) to extract its Least Significant Bit (LSB), measure its randomness via the Comparative Entropy Test, and generate a &lt;Cryptographic Master Seed&gt; using SHA-256.</p>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="entropy_file">Select Image File:</label><br>
            <input type="file" name="entropy_file" id="entropy_file" accept="image/jpeg, image/png, image/gif" required><br><br>
            <input type="submit" value="Extract & Generate Seed">
        </form>

        <?php echo $output; // Display general errors ?>

        <?php if (!empty($results)): ?>
            <div class="results">
                <h2>Analysis Results</h2>
                <p><strong>Original Image Name:</strong> <?php echo htmlspecialchars($results['original_file']); ?></p>
                <p><strong>Original Image:</strong>  <img src="<?php echo UPLOAD_DIR.$tempFileName; ?>" width="640"/></p>
                <p><strong>Raw Bits Extracted:</strong> <?php echo $results['bit_length']; ?></p>

                <hr>

                <h3>Comparative Cryptographic Entropy (<?php echo $results['block_size']; ?>-bit Blocks)</h3>
                <p style="font-size: 0.9em; color: #2c3e50;">Cryptographic security is governed by the **Min-Entropy** (H<sub>min</sub>), the worst-case measure.</p>

                <p><strong>Shannon Entropy (H1):</strong> <span class="entropy-value shannon-h1"><?php echo $results['shannon_entropy']; ?> bits/symbol</span></p>
                <p style="font-size: 0.85em; color: #3498db;">Measures **average uncertainty** (single bits). Ideal: 1.0.</p>

                <hr>

               <p><strong>Collision Entropy (H2):</strong> <span class="entropy-value collision-h2"><?php echo $results['collision_entropy']; ?> bits/block</span></p>
<p style="font-size: 0.85em; color: #f39c12;">Measures **block uniformity** (alpha=2). Ideal: <?php echo number_format($results['block_size'], 4); ?>.</p>
                <hr>

                <p><strong>Min-Entropy (H<sub>min</sub>):</strong> <span class="entropy-value min-hinf"><?php echo $results['min_entropy']; ?> bits/block</span></p>
                <p style="font-size: 0.85em; color: #c0392b; font-weight: bold;">Measures **worst-case security** (difficulty of predicting the most likely block). Ideal: <?php echo number_format($results['block_size'], 4); ?>.</p>

                <hr>

                <h3>Cryptographic Master Seed (SHA-256 Extractor):</h3>
                <p>This is the 256-bit seed generated by hashing the raw bits. Use this value to initialize a CSPRNG.</p>
                <div class="seed-box"><?php echo $results['master_seed']; ?></div>

                <hr>

                <h3>Raw Bit Output File:</h3>
                <p>Download the complete raw binary string:</p>
                <p><a href="<?php echo htmlspecialchars($fileLink); ?>" target="_blank" download>
                    <?php echo htmlspecialchars($results['output_file_name']); ?>
                </a></p>

                <p style="margin-top: 20px; font-style: italic;">If you need support with this software you can contact the author.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>