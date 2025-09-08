import json
import re

# Read the valid lines
with open('valid_lines.txt', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Parse each line and extract key-value pairs
pairs = {}
for line in lines:
    line = line.strip().rstrip(',')
    
    # Match JSON key-value pattern
    match = re.match(r'\s*"([^"]+)"\s*:\s*"([^"]*)"', line)
    if match:
        key = match.group(1)
        value = match.group(2)
        
        # Only add unique keys
        if key not in pairs:
            pairs[key] = value

# Create clean JSON
with open('en.json', 'w', encoding='utf-8') as f:
    json.dump(pairs, f, indent=2, ensure_ascii=False, sort_keys=True)

print(f'Created clean JSON with {len(pairs)} entries')
