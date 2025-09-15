import re
import sys

def fix_csv_buffer_issue(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Pattern to match the fopen line in export functions
    pattern = r'(\$out = fopen\(\'php://output\', \'w\'\);)'

    # Replacement with buffer clearing code
    replacement = r'''// Clear any output buffers to prevent extra whitespace
                while (ob_get_level()) {
                    ob_end_clean();
                }
                \1'''

    # Replace the pattern
    updated_content = re.sub(pattern, replacement, content)

    # Write back to file
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(updated_content)

    print(f"Fixed buffer issue in {file_path}")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python fix_buffer.py <file_path>")
        sys.exit(1)

    fix_csv_buffer_issue(sys.argv[1])