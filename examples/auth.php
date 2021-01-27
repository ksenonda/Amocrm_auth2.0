<?php
// авторизация по auth 2.0

use AmoCRM\OAuth2\Client\Provider\AmoCRM;
include_once '.../vendor/autoload.php';
include_once __DIR__ . '/AmoCRM.php';
// связь с бд
require_once __DIR__.'/lib/bdconfig.php';
require_once __DIR__.'/lib/classes.php';

$sql = "SELECT * FROM ... WHERE ...";
$result = $pdo->getData($sql);
$accessToken = $result[0];

//задаем данные клиента
$provider = new AmoCRM([
    'clientId' => '...',
    'clientSecret' => '...',
    'redirectUri' => '...',
]);

$accessToken = $provider->getToken($accessToken);
$provider->setBaseDomain($accessToken->getValues()['baseDomain']);
if ($accessToken->hasExpired()) 
        {
                $accessToken = $provider->getAccessToken(new League\OAuth2\Client\Grant\RefreshToken(), [
                'refresh_token' => $accessToken->getRefreshToken(),
                ]);

                $token = $accessToken->getToken();
                $refresh_token = $accessToken->getRefreshToken();
                $expires = $accessToken->getExpires();
                $base_domain = $provider->getBaseDomain();

                $sql = "UPDATE ... SET ... WHERE ...";
                $pdo->query($sql);
                
        }
$token = $accessToken->getToken();
$account_link = $provider->authDomain();

$amo = new \AmoCRM2\Client($account_link, $token); 