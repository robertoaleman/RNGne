<h2>RNGneProcessor:</h2>
<h2> Random Number Generator from Natural Entropy </h2>
 * Encapsulates the logic for extracting random bits from an image and calculating Shannon Entropy,<br/>
 * and uses a cryptographic extractor to ensure a highly strong and secure seed obtained from the entropy of natural phenomena.<br/>
 * Author: Roberto Aleman<br/>
 * Documentation: https://ventics.com/rngneprocessor/<br/> 
<h3>Introduction:True Randomness from Natural Entropy </h3>
The <b><code>RNGneProcessor</code></b> class embodies the core concept that <b>physical, chaotic phenomena are the ideal source for generating True Random Numbers (TRNGs)</b>. While algorithmic generators (PRNGs) are fast, they are mathematically predictable. True security, especially in cryptography, requires <b>unpredictability</b> drawn from the physical world.

The <b>RNGne</b> project implements a robust two-phase approach to leverage this natural chaos for secure applications:
<ol start="1">
 	<li><b>Extraction Phase (TRNG):</b> Uses an image of a chaotic natural scene (like waves or lightning) to capture the random thermal noise of the camera sensor.</li>
 	<li><b>Amplification Phase (CSPRNG Seed):</b> Converts the extracted raw entropy into a tiny, high-quality <b>Cryptographic Master Seed</b> via a cryptographic hash. This seed can then be mathematically amplified into an infinite stream of secure random numbers using a CSPRNG.</li>
</ol>
<h2>RNGneProcessor PHP Class, Documentation</h2>
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

Disclaimer. This software is provided as is, you are responsible for using it.
