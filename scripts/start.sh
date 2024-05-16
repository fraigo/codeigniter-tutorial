PORT=$(grep -E "^app.baseURL" .env | grep "127.0.0.1" | grep -o -E ':[0-9]+' | grep -o -E "[0-9]+")
if [ "$PORT" == "" ]; then
    PORT=8080
fi
echo "Using port $PORT"
rm -rf /writable/debugbar/*
php spark serve --host 127.0.0.1 --port $PORT