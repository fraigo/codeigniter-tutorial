ENV=$1
echo "Installing composer packages..."
composer install
if [ ! -e ".dev" ]; then
    echo "creating env file from $ENV.env ..."
    cp $ENV.env .env
fi
unzip -o vendor_patches.zip
echo "Running migrations..."
php spark migrate
echo "Importing app data"
php spark db:seed AppData
