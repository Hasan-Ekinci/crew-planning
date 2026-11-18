#!/usr/bin/env bash

set -euo pipefail

cd -- "$(dirname -- "${BASH_SOURCE[0]}")"

if [[ ! -f .env ]]; then
    cp .env.example .env
fi

composer install

app_key_missing="$(php -r '
require "vendor/autoload.php";

$contents = file_get_contents(".env");
$environment = Dotenv\Dotenv::parse($contents);

if (($environment["APP_KEY"] ?? "") !== "") {
    echo "no";
    exit;
}

$contents = preg_replace("/^APP_KEY=.*$/m", "APP_KEY=", $contents, -1, $count);
if ($count === 0) {
    $contents .= "\nAPP_KEY=\n";
}

file_put_contents(".env", $contents);
echo "yes";
')"

if [[ "$app_key_missing" == "yes" ]]; then
    php artisan key:generate
fi

npm ci
npm run build

printf '%s\n' \
    'Setup complete. Configure your database in .env, then run:' \
    '  php artisan migrate --seed' \
    '  composer dev'
