<?php
/**
 * Class RNGneProcessor ( Random Number Generator from Natural Entropy )
 * Encapsulates the logic for extracting random bits from an image and calculating Shannon Entropy,
 * and uses a cryptographic extractor to ensure a highly strong and secure seed obtained from the entropy of natural phenomena.
 * Author: Roberto Aleman
 * Documentation:https://ventics.com/rngneprocessor/
 */
require_once 'rngne_processor.php';

$output = '';
$fileLink = '';
$results = [];
define('UPLOAD_DIR', 'uploads/');

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

        // 4. Calculate Shannon Entropy
        $shannonEntropy = $processor->calculateShannonEntropy($rawRandomBits);

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
    <title>RNGneProcessor:Random Number Generator from Natural Entropy</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1, h2 { color: #2c3e50; border-bottom: 2px solid #bdc3c7; padding-bottom: 10px; }
        form { margin-top: 20px; padding: 20px; border: 1px dashed #3498db; border-radius: 4px; background-color: #ecf0f1; }
        input[type="file"], input[type="submit"] { padding: 10px; margin: 5px 0; border-radius: 4px; border: 1px solid #ddd; }
        input[type="submit"] { background-color: #2ecc71; color: white; cursor: pointer; border: none; font-weight: bold; }
        input[type="submit"]:hover { background-color: #27ae60; }
        .results { margin-top: 30px; padding: 20px; background-color: #e8f5e9; border: 1px solid #4CAF50; border-radius: 8px; }
        .results p { margin: 8px 0; }
        .entropy-value { font-size: 1.5em; font-weight: bold; color: #e67e22; }
        .seed-box { background-color: #34495e; color: #fff; padding: 15px; border-radius: 4px; overflow-wrap: break-word; font-family: 'Courier New', monospace; font-size: 0.9em; margin-top: 10px; }
        .error { color: #c0392b; background-color: #fdedee; padding: 10px; border: 1px solid #f9c0c0; border-radius: 4px; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>RNGne:Random Number Generator from Natural Entropy </h1><p>  Author: Roberto Aleman<br>
    <a href="https://ventics.com/rngneprocessor/">Documentation</a></p>


        <p>Upload a high-entropy image (JPEG, PNG, or GIF) to extract its Least Significant Bit (LSB), measure its randomness via the Shannon Test, and generate a <Cryptographic Master Seed> using SHA-256.</p>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="entropy_file">Select Image File:</label><br>
            <input type="file" name="entropy_file" id="entropy_file" accept="image/jpeg, image/png, image/gif" required><br><br>
            <input type="submit" value="Extract & Generate Seed">
        </form>

        <?php echo $output; // Display general errors ?>

        <?php if (!empty($results)): ?>
            <div class="results">
                <h2>Analysis Results</h2>
                <p><strong>Original Image:</strong> <?php echo htmlspecialchars($results['original_file']); ?></p>
                <p><strong>Raw Bits Extracted:</strong> <?php echo $results['bit_length']; ?></p>

                <hr>

                <h3>Shannon Entropy Test (H):</h3>
                <p class="entropy-value"><?php echo $results['shannon_entropy']; ?> bits/symbol</p>
                <p style="font-size: 0.9em; color: #2c3e50;">(Ideal maximum value is 1.0. A value closer to 1.0 indicates higher randomness and purity.)</p>

                <hr>

                <h3>Cryptographic Master Seed (SHA-256 Extractor):</h3>
                <p>This is the 256-bit seed generated by hashing the raw bits.  Use this value  to initialize a CSPRNG.</p>
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


