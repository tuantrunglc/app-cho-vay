#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

console.log('🔍 Checking App Vay Frontend Setup...\n');

// Check if required files exist
const requiredFiles = [
  'package.json',
  'vite.config.js',
  'tailwind.config.js',
  'postcss.config.js',
  'index.html',
  'src/main.js',
  'src/App.vue',
  'src/router/index.js'
];

console.log('📁 Checking required files:');
requiredFiles.forEach(file => {
  const exists = fs.existsSync(path.join(__dirname, file));
  console.log(`  ${exists ? '✅' : '❌'} ${file}`);
});

// Check if required directories exist
const requiredDirs = [
  'src/components',
  'src/views',
  'src/stores',
  'src/layouts',
  'src/assets',
  'src/styles'
];

console.log('\n📂 Checking required directories:');
requiredDirs.forEach(dir => {
  const exists = fs.existsSync(path.join(__dirname, dir));
  console.log(`  ${exists ? '✅' : '❌'} ${dir}`);
});

// Check package.json dependencies
console.log('\n📦 Checking dependencies:');
try {
  const packageJson = JSON.parse(fs.readFileSync(path.join(__dirname, 'package.json'), 'utf8'));
  const requiredDeps = ['vue', 'vue-router', 'pinia'];
  const requiredDevDeps = ['@vitejs/plugin-vue', 'vite', 'tailwindcss', 'autoprefixer', 'postcss'];
  
  requiredDeps.forEach(dep => {
    const exists = packageJson.dependencies && packageJson.dependencies[dep];
    console.log(`  ${exists ? '✅' : '❌'} ${dep} (dependency)`);
  });
  
  requiredDevDeps.forEach(dep => {
    const exists = packageJson.devDependencies && packageJson.devDependencies[dep];
    console.log(`  ${exists ? '✅' : '❌'} ${dep} (devDependency)`);
  });
} catch (error) {
  console.log('  ❌ Error reading package.json');
}

// Check key Vue files
console.log('\n🔧 Checking key Vue files:');
const keyFiles = [
  'src/views/customer/Home.vue',
  'src/views/customer/LoanApplication.vue',
  'src/views/admin/Dashboard.vue',
  'src/layouts/CustomerLayout.vue',
  'src/layouts/AdminLayout.vue',
  'src/stores/auth.js',
  'src/stores/loan.js',
  'src/stores/user.js',
  'src/stores/content.js'
];

keyFiles.forEach(file => {
  const exists = fs.existsSync(path.join(__dirname, file));
  console.log(`  ${exists ? '✅' : '❌'} ${file}`);
});

// Check for common issues
console.log('\n🔍 Checking for common issues:');

// Check if node_modules exists
const nodeModulesExists = fs.existsSync(path.join(__dirname, 'node_modules'));
console.log(`  ${nodeModulesExists ? '✅' : '❌'} node_modules directory (run 'npm install' if missing)`);

// Check if dist exists (build output)
const distExists = fs.existsSync(path.join(__dirname, 'dist'));
console.log(`  ${distExists ? '✅' : '⚠️'} dist directory (created after 'npm run build')`);

console.log('\n🎉 Setup check complete!');
console.log('\n📝 Next steps:');
console.log('  1. Run: npm install');
console.log('  2. Run: npm run dev');
console.log('  3. Open: http://localhost:3000');