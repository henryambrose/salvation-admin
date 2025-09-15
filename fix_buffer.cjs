const fs = require('fs');
const path = require('path');

function fixCsvBufferIssue(filePath) {
    try {
        const content = fs.readFileSync(filePath, 'utf8');

        // Pattern to match the fopen line in export functions
        const pattern = /(\$out = fopen\('php:\/\/output', 'w'\);)/g;

        // Replacement with buffer clearing code
        const replacement = `// Clear any output buffers to prevent extra whitespace
                while (ob_get_level()) {
                    ob_end_clean();
                }
                $1`;

        // Replace the pattern
        const updatedContent = content.replace(pattern, replacement);

        // Write back to file
        fs.writeFileSync(filePath, updatedContent, 'utf8');

        console.log(`Fixed buffer issue in ${filePath}`);
    } catch (error) {
        console.error(`Error fixing ${filePath}:`, error.message);
    }
}

if (process.argv.length < 3) {
    console.log('Usage: node fix_buffer.cjs <file_path>');
    process.exit(1);
}

fixCsvBufferIssue(process.argv[2]);