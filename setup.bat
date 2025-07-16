@echo off
echo 🚀 Setting up Loan Management System...

REM Backend setup
echo 📦 Setting up Backend...
cd backend

REM Copy environment file
if not exist .env (
    copy .env.example .env
    echo ✅ Created .env file
)

REM Start Docker containers
echo 🐳 Starting Docker containers...
docker-compose up -d --build

REM Wait for containers to be ready
echo ⏳ Waiting for containers to be ready...
timeout /t 30 /nobreak > nul

REM Install dependencies
echo 📦 Installing PHP dependencies...
docker-compose exec app composer install

REM Generate application key
echo 🔑 Generating application key...
docker-compose exec app php artisan key:generate

REM Generate JWT secret
echo 🔐 Generating JWT secret...
docker-compose exec app php artisan jwt:secret

REM Run migrations
echo 🗄️ Running database migrations...
docker-compose exec app php artisan migrate

REM Seed database
echo 🌱 Seeding database...
docker-compose exec app php artisan db:seed

REM Set permissions
echo 🔒 Setting permissions...
docker-compose exec app chmod -R 775 storage bootstrap/cache

echo ✅ Backend setup completed!

REM Frontend setup
echo 📦 Setting up Frontend...
cd ..\frontend

REM Check if package.json exists
if exist package.json (
    echo 📦 Installing Node.js dependencies...
    npm install
    
    REM Copy environment file
    if not exist .env.local (
        copy .env.example .env.local
        echo ✅ Created .env.local file
    )
    
    echo ✅ Frontend setup completed!
) else (
    echo ⚠️ Frontend not found, skipping...
)

cd ..

echo.
echo 🎉 Setup completed successfully!
echo.
echo 📋 Next steps:
echo 1. Backend API: http://localhost:8000
echo 2. Frontend App: http://localhost:3000 (run 'npm start' in frontend folder)
echo.
echo 🔑 Default admin accounts:
echo - Super Admin: admin / admin123
echo - Loan Officer: loan_officer / loan123
echo - Customer Service: cs_staff / cs123
echo.
echo 📚 Check README.md for detailed documentation

pause