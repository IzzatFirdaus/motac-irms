import json
import re

def clean_json_file():
    # Read the conflicted file
    with open('lang/en.json', 'r', encoding='utf-8') as f:
        content = f.read()

    # Remove merge conflict markers
    content = re.sub(r'<<<<<<< HEAD\n', '', content)
    content = re.sub(r'=======\n', '', content)
    content = re.sub(r'>>>>>>> [a-f0-9]+\n', '', content)

    # Extract all key-value pairs from the JSON structure
    pairs = {}

    lines = content.split('\n')
    for line in lines:
        line = line.strip()
        if not line or line.startswith('//') or line in ['{', '}']:
            continue

        # Remove trailing commas
        line = line.rstrip(',')

        # Try to extract key-value pairs using regex for JSON format
        match = re.search(r'"([^"]+)"\s*:\s*"([^"]*)"', line)
        if match:
            key = match.group(1)
            value = match.group(2)
            # Only add if not already present (avoid duplicates)
            if key not in pairs:
                pairs[key] = value

    # Create clean JSON
    clean_json = json.dumps(pairs, indent=2, ensure_ascii=False, sort_keys=True)

    # Write the clean version
    with open('lang/en.json', 'w', encoding='utf-8') as f:
        f.write(clean_json)

    print(f'Extracted {len(pairs)} unique key-value pairs')
    print('Clean JSON written successfully')

if __name__ == "__main__":
    clean_json_file()
