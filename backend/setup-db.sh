#!/bin/bash

echo "🗄️ Setting up database..."

# Run migrations
echo "📦 Running migrations..."
php artisan migrate

# Run seeders
echo "🌱 Running seeders..."
php artisan db:seed

echo "✅ Database setup completed!"
echo ""
echo "🔑 Default admin accounts:"
echo "- Super Admin: admin / admin123"
echo "- Loan Officer: loan_officer / loan123"
echo "- Customer Service: cs_staff / cs123"