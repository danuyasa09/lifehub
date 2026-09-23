const fs = require('fs');
const path = require('path');

const directoryPath = path.join(__dirname, 'resources', 'views');

const replacements = {
    'bg-white': 'bg-surface',
    'bg-gray-50': 'bg-surface-secondary',
    'bg-gray-100': 'bg-surface-secondary',
    'bg-gray-200': 'bg-surface-secondary',
    'border-gray-100': 'border-border',
    'border-gray-200': 'border-border',
    'border-gray-300': 'border-border',
    'border-gray-400': 'border-border',
    'border-gray-500': 'border-border',
    'text-gray-900': 'text-text',
    'text-gray-800': 'text-text',
    'text-gray-700': 'text-text',
    'text-dark': 'text-text',
    'text-gray-600': 'text-text-secondary',
    'text-gray-500': 'text-text-secondary',
    'text-gray-400': 'text-text-secondary',
    'ring-gray-100': 'ring-border',
    'ring-gray-200': 'ring-border',
    'ring-gray-300': 'ring-border',
    'divide-gray-100': 'divide-border',
    'divide-gray-200': 'divide-border',
};

function processFile(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    let modified = false;

    for (const [oldClass, newClass] of Object.entries(replacements)) {
        // Regex to match oldClass with optional prefixes (like hover:, focus:) and optional opacity (/50)
        // Ensure it's surrounded by space, quote, or backtick
        // Pattern: (?<=[\s"'`])([a-z0-9-]*:)*oldClass(?:\/\d+)?(?=[\s"'`])
        // To do replace with matched prefixes/opacity, we need capture groups.
        const regex = new RegExp(`(^|[\\s"'\\\`])([a-z0-9-:]*)(${oldClass})((?:\\/\\d+)?)([\\s"'\\\`]|$)`, 'g');
        
        let newContent = content.replace(regex, (match, p1, p2, p3, p4, p5) => {
            return p1 + p2 + newClass + p4 + p5;
        });

        if (newContent !== content) {
            content = newContent;
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
        } else if (fullPath.endsWith('.blade.php') && !fullPath.includes('welcome.blade.php')) {
            processFile(fullPath);
        }
    }
}

walkDir(directoryPath);
console.log('Done!');
