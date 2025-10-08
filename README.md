<h2>RNGne Processor:</h2>
<h2> Random Number Generator from Natural Entropy </h2>
 * Encapsulates the logic for extracting random bits from an image and calculating Shannon Entropy,<br/>
 * and uses a cryptographic extractor to ensure a highly strong and secure seed obtained from the entropy of natural phenomena.<br/>
 * Author: Roberto Aleman<br/>
 * Documentation: <br>
 v1: https://ventics.com/rngneprocessor/
 <br/> 
 v2: https://ventics.com/rngne-processor-v2-0/ <br/> 
<h3>Introduction:True Randomness from Natural Entropy </h3>
The <b><code>RNGneProcessor</code></b> class embodies the core concept that <b>physical, chaotic phenomena are the ideal source for generating True Random Numbers (TRNGs)</b>. While algorithmic generators (PRNGs) are fast, they are mathematically predictable. True security, especially in cryptography, requires <b>unpredictability</b> drawn from the physical world.

The <b>RNGne</b> project implements a robust two-phase approach to leverage this natural chaos for secure applications:
<ol start="1">
 	<li><b>Extraction Phase (TRNG):</b> Uses an image of a chaotic natural scene (like waves or lightning) to capture the random thermal noise of the camera sensor.</li>
 	<li><b>Amplification Phase (CSPRNG Seed):</b> Converts the extracted raw entropy into a tiny, high-quality <b>Cryptographic Master Seed</b> via a cryptographic hash. This seed can then be mathematically amplified into a theoretically infinite stream of secure random numbers using a CSPRNG.</li>
</ol>
<h2>RNGne Processor PHP Class, Documentation</h2>
<h3>Core Operations and Methodology</h3>
The system operates by extracting the most volatile and noisy part of the image data: the <b>Least Significant Bit (LSB)</b>.
<h4>1. Entropy Extraction (LSB Method)</h4>
The <code>processImageForRandomBits()</code> method performs the following low-level data extraction:
<ul>
 	<li><b>Pixel Iteration:</b> The function iterates through every pixel in the uploaded image.</li>
 	<li><b>Grayscale Conversion:</b> It averages the Red, Green, and Blue (RGB) components to find the single grayscale intensity value for that pixel.</li>
 	<li><b>LSB Isolation:</b> It isolates the <b>Least Significant Bit (LSB)</b> of the grayscale value using the modulo operator (<code>% 2</code>). This LSB is the part of the pixel data most likely to be affected by the unpredictable <b>thermal and quantum noise</b> of the camera sensor, making it the purest source of entropy.</li>
</ul>
<h4>2. Entropy Quality Test (Shannon's Formula)</h4>
The <code>calculateShannonEntropy()</code> method is the <b>validation metric</b> for the quality of the raw bits:
<ul>
 	<li><b>Purpose:</b> It measures the <b>uncertainty</b> or the average information content of the bit stream.</li>
 	<li><b>Metric:</b> The result is given in <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord text"><span class="mord">bits/symbol</span></span></span></span></span></span>. A value of <b><span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">1.0000</span></span></span></span></span></b> signifies a statistically perfect, uniform distribution of '0's and '1's (i.e., maximum randomness). Any deviation below <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">1.0000</span></span></span></span></span> indicates a measurable bias or pattern.</li>
</ul>
<h4>3. Cryptographic Extractor (SHA-256)</h4>
The <code>extractMasterSeed()</code> method performs the critical security transition:
<ul>
 	<li><b>Input:</b> The long string of raw, high-entropy bits.</li>
 	<li><b>Process:</b> It passes the entire bit string through the irreversible and collision-resistant <b>SHA-256</b> hash function.</li>
 	<li><b>Output:</b> The result is a <b>256-bit Cryptographic Master Seed</b> (a 64-character hexadecimal string). This process cleanses any remaining weak bias from the raw source and ensures the final seed is uniformly random and secure.</li>
</ul>
<h3>Validation Results: Proving Purity and Precision</h3>
To validate the <b><code>RNGneProcessor</code></b>, tests were conducted using sources ranging from zero entropy (control) to maximum entropy (ideal source).
<table>
<thead>
<tr>
<td>Source Image</td>
<td>Description</td>
<td>Result (Shannon H)</td>
<td>Conclusion</td>
</tr>
</thead>
<tbody>
<tr>
<td><b>Waves</b></td>
<td>High-complexity natural scene (Chaos, thermal noise).</td>
<td><b><span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">1.0000</span></span></span></span></span> bits/pixel</b></td>
<td><b>Ideal Source Validation.</b> Confirms the LSB of chaotic natural images yields maximum, unbiased entropy.</td>
</tr>
<tr>
<td><b>White square
</b></td>
<td>Artificially uniform, pure white image (Control).</td>
<td><b><span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">0.0000</span></span></span></span></span> bits/pixel</b></td>
<td><b>Zero Entropy Control.</b> Confirms that predictable data (all LSBs are '1's) results in zero uncertainty.</td>
</tr>
<tr>
<td><b>Black Square
</b></td>
<td>Artificially uniform, pure black image (Control).</td>
<td><b><span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">0.0000</span></span></span></span></span> bits/pixel</b></td>
<td><b>Zero Entropy Control.</b> Confirms that predictable data (all LSBs are '0's) results in zero uncertainty</td>
</tr>
</tbody>
</table>
The project's precision is demonstrated by its ability to accurately measure the entire range: from perfect predictability (<span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">0.0000</span></span></span></span></span>) to the highest level of natural chaos (<span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">1.0000</span></span></span></span></span>).
<h3>Requirements and Usage</h3>
<b>RNGneProcessor</b> is distributed for free under an open-source license.

<b>Requirements:</b>
<ul>
 	<li>PHP 7.0+</li>
 	<li><b>GD Extension</b> (for image manipulation)</li>
</ul>
<b>Best Practices for High Entropía Sources:</b> To ensure the best possible <b>Master Seed</b>, users should prioritize images that maximize the sensor noise:
<ul>
 	<li>Photos of chaotic elements: <b>Lightning, ocean waves, dense foliage.</b></li>
 	<li>Photos taken in <b>low light conditions</b> (which amplifies thermal noise).</li>
 	<li>Use <b>PNG</b> or <b>RAW</b> image formats (lossless) to avoid compression artifacts that reduce real entropy.</li>
</ul>
<h2>Installation and Testing Guide for RNGne</h2>
This guide will walk you through setting up and validating the <b>RNGne (Random Number Generator from Natural Entropy)</b>
<h2>Step 1: File Setup</h2>
<ol start="1">
 	<li><b>Create the Project Folder:</b> Navigate to your web server's root directory and create a new folder named <code>rngne</code>.</li>
 	<li><b>Save the Code:</b> Place the two PHP files inside this new <code>rngne</code> folder.
<ul>
 	<li><b><code>RNGneProcessor.php</code></b> (The class containing the LSB extraction and SHA-256 logic).</li>
 	<li><b><code>upload_and_process.php</code></b> (The web interface and controller).</li>
</ul>
</li>
 	<li><b>Create the Upload Directory:</b> Inside the <code>rngne</code> folder, create a new subdirectory named <b><code>uploads</code></b>.</li>
 	<li><b>Permissions:</b> Ensure the <b><code>uploads</code></b> folder has <b>write permissions</b> (This is usually automatic on local Windows setups, but is essential for Linux/production servers).</li>
</ol>
<h3>Your Folder Structure Should Look Like This:</h3>
<pre><code class="code-container formatted ng-tns-c3543900489-207 no-decoration-radius" role="text" data-test-id="code-content">/rngne
|- RNGneProcessor.php
|- upload_and_process.php
|- /uploads (Empty folder, must be writable)</code></pre>
<h2>Step 2: Acquire a Test Image</h2>
To perform validation tests, you'll need two types of images:
<ol start="1">
 	<li><b>High-Entropy Image (Ideal Source):</b> An image of pure chaos, like ocean waves, static, deep-sky photography, or lightning. This should yield a Shannon Entropy of <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mrel">?</span></span><span class="base"><span class="mord">1.0000</span></span></span></span></span>.</li>
 	<li><b>Zero-Entropy Image (Control Source):</b> A solid black or solid white image. This should yield a Shannon Entropy of <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">0.0000</span></span></span></span></span>.</li>
</ol>
<h2>Step 3: Execution and Validation</h2>
<ol start="1">
 	<li><b>Start Your Server:</b> Ensure your Apache web server is running.</li>
 	<li><b>Open the Tool:</b> Open your web browser and navigate to your project URL (e.g., <code>http://yourtestdomain/rngne/upload_and_process.php</code>).</li>
 	<li><b>Perform Control Test (Zero Entropy):</b>
<ul>
 	<li>Click <b>"Select Image File"</b> and upload your <b>solid black or white</b> control image.</li>
 	<li>Click <b>"Extract &amp; Generate Seed"</b>.</li>
 	<li><b>Validation Check:</b> The <b>Shannon Entropy Test (H)</b> result should be near <b><span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">0.0000</span><span class="mord text"><span class="mord"> bits/symbol</span></span></span></span></span></span></b>.</li>
</ul>
</li>
 	<li><b>Perform Entropy Test (Ideal Source):</b>
<ul>
 	<li>Click <b>"Select Image File"</b> and upload your <b>high-entropy image</b> (e.g., the image of the waves).</li>
 	<li>Click <b>"Extract &amp; Generate Seed"</b>.</li>
 	<li><b>Validation Check:</b> The <b>Shannon Entropy Test (H)</b> result should be near <b><span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">1.0000</span><span class="mord text"><span class="mord"> bits/symbol</span></span></span></span></span></span></b>.</li>
</ul>
</li>
</ol>
<img  src="https://ventics.com/wp-content/uploads/2025/10/ventics.com_RNGne-1.jpg" alt="" width="891" height="476" /> 

<img  src="https://ventics.com/wp-content/uploads/2025/10/ventics.com_RNGne-2.jpg" alt="" width="818" height="626" /> 
If both tests pass, your <b>RNGne</b> system is correctly installed and functioning, and it is accurately converting the physical chaos of the image into a high-quality <b>Cryptographic Master Seed</b>. 

-----------------

<h2>RNGne Processor v2.0: From Nature to Solid Cryptographic Seed</h2>

Introduction: Raising the Quality of Natural Entropy
The class RNGneProcessorhas evolved to version 2.0, consolidating its position as a robust tool for generating random seeds from natural phenomena (images). The core objective remains to extract the physical randomness inherent in image noise (vortices, waves, chaotic patterns) and purify it into a Cryptographic Master Seed using SHA-256.

The crucial improvement in this release is the integration of Rényi Entropies , enabling a security audit that goes beyond basic statistical analysis.

The Methodology: TRNG with Crypto Extractor
The workflow RNGneProcessorcombines the physics of noise with the mathematics of safety:

Raw Entropy Harvesting ( Harvester ): The function processImageForRandomBits()extracts the Least Significant Bit (LSB) from each pixel, collecting the weakest and most chaotic noise source from the image.
Quality Audit (Entropy): The quality of the extracted raw bits is measured using three different metrics, with emphasis on the “worst case.”
Purity Extraction (Cryptographic Extraction): The function extractMasterSeed()uses SHA-256 to compress and remove any remaining bias in the raw entropy, ensuring a final 256-bit seed of complete purity.

# The New Frontier: Rényi's Entropy for Security

While Shannon entropy ( H1 ) is excellent for measuring overall imbalance (i.e., whether there are more zeros than ones), cryptography requires a more stringent measure. This is where Rényi entropies come in, measured in 8-bit (1-byte) blocks for standard security analysis:

# Collision Entropy ( Hmin or α=2 )
What it measures: Block uniformity . It focuses on how far the distribution of the 256 possible bytes is from being perfectly flat. A value close to 8.0000 (for an 8-bit block) is ideal.

Significance: Helps confirm that the LSB extraction process is not favoring certain byte patterns beyond single-bit bias.

# Minimum Entropy ( Hmin or H∞ )
What it measures: Worst-case security . This is the most important metric. It's calculated from the probability of the most frequent block in the entire bit sequence.

# Hmin = −log2  ( Probability of the Most Frequent Block )

Importance: Hmin is the actual security value of the raw source. If the result is 7.8271 (as in real-life examples), it means the source is not perfectly random, fully justifying the use of the Crypto Extractor . Without this analysis, we would not know the risk level of the raw material.

# Conclusion: The Necessary Cryptography

Comparing the results (ej., H1​ ≈1.0000 vs. Hmin ≈7.8271) is the acid test:

H1​ perfect(1.0) suggests a large statistical source.
Hmin imperfect(e.g., 7.8271) reveals atiny deviationinherent in the physical process.

This small deviation is what the SHA-256 Extractor deterministically corrects . By hashing, the residual predictability (the missing 0.1729 bits) is removed and the final Master Seed is guaranteed to be a full 256 bits pure , ideal for initializing any Cryptographically Secure Pseudo-Random Number Generator (CSPRNG).

He RNGneProcessor v2.0 not only extracts randomness from nature, but uses the most rigorous tools of cryptography to certify and purify that randomness, making it suitable for the most demanding security environments.

<h3>What the Class Does (The Process)</h3>
&nbsp;

The <code>RNGneProcessor</code> class executes a three-stage pipeline to generate a master cryptographic seed:
<ol start="1">
 	<li><b>Entropy Harvester (Physical Input):</b> The <code>processImageForRandomBits()</code> method extracts the <b>Least Significant Bit (LSB)</b> from the grayscale value of every pixel. This LSB acts as a collector of tiny, unpredictable physical noise (atmospheric disturbance, sensor noise) present in the image, yielding a long string of raw, <i>physical</i> random bits.</li>
 	<li><b>Comparative Cryptographic Audit (Rényi Analysis):</b> The class calculates three critical entropy measures on the raw bit string (analyzed in 8-bit blocks):
<ul>
 	<li><b>Shannon Entropy (<span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathnormal">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight">1</span></span></span></span><span class="vlist-s">​</span></span></span></span></span></span></span></span></span>):</b> Measures the <b>average uncertainty</b> (basic <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">0</span></span></span></span></span> vs. <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">1</span></span></span></span></span> balance).</li>
 	<li><b>Collision Entropy (<span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathnormal">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight">2</span></span></span></span><span class="vlist-s">​</span></span></span></span></span></span></span></span></span>):</b> Measures the <b>uniformity of the blocks</b> (how flat the distribution of all 256 possible bytes is).</li>
 	<li><b>Min-Entropy (<span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathnormal">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord text mtight">min</span></span></span></span></span><span class="vlist-s">​</span></span></span></span></span></span></span></span></span> or <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathnormal">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight">∞</span></span></span></span><span class="vlist-s">​</span></span></span></span></span></span></span></span></span>):</b> Measures the <b>worst-case security</b>. This identifies the probability of the <i>single most likely block</i> appearing, providing the true, minimum amount of randomness (in bits) available in the raw data.</li>
</ul>
</li>
 	<li><b>Cryptographic Extractor:</b> The <code>extractMasterSeed()</code> method uses the <b>SHA-256</b> cryptographic hash function to process the raw bits. This step is essential because it eliminates any remaining statistical bias detected by the <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathnormal">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord text mtight">min</span></span></span></span></span><span class="vlist-s">​</span></span></span></span></span></span></span></span></span> test.</li>
</ol>
&nbsp;
<h3>What is Achieved with Version 2.0</h3>
&nbsp;

The key achievement of V2.0 is the transition from a mere random bit generator to a <b>certified cryptographic seed producer</b> by explicitly addressing worst-case scenarios.
<ol start="1">
 	<li><b>Quantified Security Level:</b> The most important achievement is obtaining the <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathbf">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord text mtight">min</span></span></span></span></span><span class="vlist-s">​</span></span></span></span></span></span></span></span></span> value. This metric tells you exactly, in bits, how much true randomness is in your raw source (e.g., <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord">7.8271</span></span></span></span></span> bits/block). This is the <b>cryptographic standard</b> for assessing the quality of a random source, which Shannon Entropy (<span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathnormal">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight">1</span></span></span></span><span class="vlist-s">​</span></span></span></span></span></span></span></span></span>) cannot provide.</li>
 	<li><b>Justification of the Extractor:</b> By showing that the raw entropy is typically high but slightly less than ideal (e.g., <span class="math-inline"><span class="katex"><span class="katex-html" aria-hidden="true"><span class="base"><span class="mord"><span class="mord mathnormal">H</span><span class="msupsub"><span class="vlist-t vlist-t2"><span class="vlist-r"><span class="vlist"><span class=""><span class="sizing reset-size6 size3 mtight"><span class="mord mtight"><span class="mord text mtight">min</span></span></span></span></span><span class="vlist-s">​</span></span></span></span></span><span class="mrel">&lt;</span></span><span class="base"><span class="mord">8.0000</span></span></span></span></span>), the analysis <b>cryptographically justifies</b> the need for the SHA-256 extractor. The hash function acts as a high-quality <b>"purifier"</b> that guarantees the final 256-bit seed is free of <i>all</i> detectable bias, making it suitable for initializing a <b>CSPRNG (Cryptographically Secure Pseudo-Random Number Generator)</b>.</li>
 	<li><b>Academic and Speculative Value:</b> The comparative analysis facilitates deeper study (as we discussed), allowing users to quantify the <b>"complexity"</b> or <b>"chaotic existence"</b> of different natural phenomena. It provides a rigorous, universal metric to compare the randomness inherent in different physical sources.</li>
</ol>

------------------
Disclaimer. This software is provided as is. You are responsible for testing it at your own risk.
