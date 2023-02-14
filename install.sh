ENV=$1
echo "Installing composer packages..."
composer update
echo "Copying env file ($ENV.env)..."
cp $ENV.env .env
echo "Running migrations..."
php spark migrate
echo "Importing app data"
php spark db:seed AppData
