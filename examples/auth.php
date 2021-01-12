<?php
use AmoCRM\OAuth2\Client\Provider\AmoAuth;

include_once '/var/www/vexor/vendor/autoload.php';
include_once __DIR__ . '/AmoCRM.php';

//задаем данные клиента
$provider = new AmoAuth([
    'clientId' => '',
    'clientSecret' => '',
    'redirectUri' => '',
]);
//проверка токена
$accessToken = $provider->getToken('token_file.txt');
$provider->setBaseDomain($accessToken->getValues()['baseDomain']);
if ($accessToken->hasExpired()) {
    try {
        $accessToken = $provider->getAccessToken(new League\OAuth2\Client\Grant\RefreshToken(), [
            'refresh_token' => $accessToken->getRefreshToken(),
        ]);
        $provider->saveToken([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'expires' => $accessToken->getExpires(),
            'baseDomain' => $provider->getBaseDomain(),
        ], 'token_file.txt');
    } catch (Exception $e) {
        die((string)$e);
    }
}

//получение токена для дальнейших запросов
$token = $accessToken->getToken('token_file.txt');
//формируем поля заголовков для запросов
$headers = [
    'Content-Type:application/json',
    'Authorization: Bearer ' . $token,
];
//ссылка к кабинету клиента
$full_domain = $provider->urlAccount(); 