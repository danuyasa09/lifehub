const fs = require('fs');
const path = require('path');

const directoryPath = path.join(__dirname, 'resources', 'views');

const replacements = {
    'bg-white': 'bg-surface',
    'bg-gray-50': 'bg-surface-secondary',
    'bg-gray-100': 'bg-surface-secondary',
    'border-gray-100': 'border-border',
    'border-gray-200': 'border-border',
    'border-gray-300': 'border-border',
    'border-gray-400': 'border-border',
    'text-gray-900': 'text-text',
    'text-gray-800': 'text-text',
    'text-gray-700': 'text-text',
    'text-dark': 'text-text',
    'text-gray-600': 'text-text-secondary',
    'text-gray-500': 'text-text-secondary',
    'text-gray-400': 'text-text-secondary',
    'ring-gray-200': 'ring-border',
    'ring-gray-300': 'ring-border',
    'divide-gray-100': 'divide-border',
    'divide-gray-200': 'divide-border',
};

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;

    // Use regex to match exact words (classes)
    for (const [oldClass, newClass] of Object.entries(replacements)) {
        // Match oldClass bordered by word boundaries or quotes/spaces
        const regex = new RegExp(`(?<=[\\s"'\\\`])` + oldClass + `(?=[\\s"'\\\`])`, 'g');
        if (regex.test(content)) {
            content = content.replace(regex, newClass);
            modified = true;
        }
    }

    if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`Updated: ${filePath}`);
    }
}

function walkDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            walkDir(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            processFile(fullPath);
        }
    }
}

walkDir(directoryPath);
console.log('Done!');
