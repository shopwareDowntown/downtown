<?php declare(strict_types=1);

use Shopware\Core\Framework\Test\TestCaseBase\KernelLifecycleManager;
use Shopware\Core\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$_SERVER['PROJECT_ROOT'] = $_SERVER['PROJECT_ROOT'] ?? dirname(__DIR__);
$_ENV['PROJECT_ROOT'] = $_SERVER['PROJECT_ROOT'];
define('TEST_PROJECT_DIR', $_SERVER['PROJECT_ROOT']);

$testEnv = [
    'APP_ENV' => 'test',
    'APP_DEBUG' => 1,
    'APP_SECRET' => 's$cretf0rt3st',
    'KERNEL_CLASS' => Kernel::class,
    'SHOPWARE_ES_ENABLED' => '',
    'SHOPWARE_ES_INDEXING_ENABLED' => '',
    'JWT_PRIVATE_KEY_PASSPHRASE' => 'shopware',
    'VERSION' => $_SERVER['VERSION'] ?? $_SERVER['BUILD_VERSION'] ?? 'v6.1.0'
];

foreach ($testEnv as $key => $value) {
    $_SERVER[$key] = $value;
    $_ENV[$key] = $_SERVER[$key];
}

$jwtDir = TEST_PROJECT_DIR . '/var/test/jwt';

if (!file_exists($jwtDir) && !mkdir($jwtDir, 0770, true) && !is_dir($jwtDir)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $jwtDir));
}

// generate jwt pk
$key = openssl_pkey_new([
    'digest_alg' => 'aes256',
    'private_key_type' => OPENSSL_KEYTYPE_RSA,
    'private_key_bits' => 4096,
    'encrypt_key_cipher' => OPENSSL_CIPHER_AES_256_CBC,
    'encrypt_key' => $_SERVER['JWT_PRIVATE_KEY_PASSPHRASE']
]);

// export private key
openssl_pkey_export_to_file($key, $jwtDir . '/private.pem');

// export public key
$keyData = openssl_pkey_get_details($key);
file_put_contents($jwtDir . '/public.pem', $keyData['key']);

chmod($jwtDir . '/private.pem', 0660);
chmod($jwtDir . '/public.pem', 0660);

$loader = require TEST_PROJECT_DIR . '/vendor/autoload.php';

KernelLifecycleManager::prepare($loader);

if (file_exists(TEST_PROJECT_DIR . '/.env.test')) {
    if (!class_exists(Dotenv::class)) {
        throw new RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env.test file.');
    }
    (new Dotenv(true))->load(TEST_PROJECT_DIR . '/.env.test');
}
