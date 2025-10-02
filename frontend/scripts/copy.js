const fs = require('fs');
const path = require('path');

const src = path.resolve(__dirname, '../../public/build');
const dest = path.resolve(__dirname, '../dist');

function rimraf(p) {
    if (fs.existsSync(p)) {
        fs.rmSync(p, { recursive: true, force: true });
    }
}

function copyDir(from, to) {
    fs.mkdirSync(to, { recursive: true });
    for (const entry of fs.readdirSync(from, { withFileTypes: true })) {
        const srcPath = path.join(from, entry.name);
        const destPath = path.join(to, entry.name);
        if (entry.isDirectory()) copyDir(srcPath, destPath);
        else fs.copyFileSync(srcPath, destPath);
    }
}

if (!fs.existsSync(src)) {
    console.error('Source build folder not found:', src);
    process.exit(1);
}

rimraf(dest);
copyDir(src, dest);
console.log('Copied', src, '→', dest);


