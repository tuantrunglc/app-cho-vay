#!/bin/bash

echo "🚀 Setting up Loan Management System..."

# Backend setup
echo "📦 Setting up Backend..."
cd backend

# Copy environment file
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Created .env file"
fi

# Start Docker containers
echo "🐳 Starting Docker containers..."
docker-compose up -d --build

# Wait for containers to be ready
echo "⏳ Waiting for containers to be ready..."
sleep 30

# Install dependencies
echo "📦 Installing PHP dependencies..."
docker-compose exec app composer install

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec app php artisan key:generate

# Generate JWT secret
echo "🔐 Generating JWT secret..."
docker-compose exec app php artisan jwt:secret

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose exec app php artisan migrate

# Seed database
echo "🌱 Seeding database..."
docker-compose exec app php artisan db:seed

# Set permissions
echo "🔒 Setting permissions..."
docker-compose exec app chmod -R 775 storage bootstrap/cache

echo "✅ Backend setup completed!"

# Frontend setup
echo "📦 Setting up Frontend..."
cd ../frontend

# Install dependencies
if [ -f package.json ]; then
    echo "📦 Installing Node.js dependencies..."
    npm install
    
    # Copy environment file
    if [ ! -f .env.local ]; then
        cp .env.example .env.local
        echo "✅ Created .env.local file"
    fi
    
    echo "✅ Frontend setup completed!"
else
    echo "⚠️ Frontend not found, skipping..."
fi

cd ..

echo ""
echo "🎉 Setup completed successfully!"
echo ""
echo "📋 Next steps:"
echo "1. Backend API: http://localhost:8000"
echo "2. Frontend App: http://localhost:3000 (run 'npm start' in frontend folder)"
echo ""
echo "🔑 Default admin accounts:"
echo "- Super Admin: admin / admin123"
echo "- Loan Officer: loan_officer / loan123"
echo "- Customer Service: cs_staff / cs123"
echo ""
echo "📚 Check README.md for detailed documentation"