<?php

namespace AmoCRM\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class AmoAuth extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    public $baseDomain = 'www.amocrm.ru';

    /**
     * @var string
     */
    public $protocol = 'https://';

    /**
     * @var array
     */
    public $scopes = [];

    /**
     * @var string
     */
    public $authorizationHeader = 'Bearer';

    /**
     * @var array
     */
    public $headers = [
        'User-Agent' => 'amoCRM/oAuth Client 1.0',
    ];

    /**
     * AmoCRM constructor.
     * @param array $options
     * @param array $collaborators
     */
    public function __construct($options = [], $collaborators = [])
    {
        parent::__construct($options, $collaborators);

        if (isset($options['baseDomain'])) {
            $this->baseDomain = $options['baseDomain'];
        }
    }

    /**
     * @param string $domain
     */
    public function setBaseDomain($domain)
    {
        $this->baseDomain = $domain;
    }

    /**
     * @return string
     */
    public function getBaseDomain()
    {
        return $this->baseDomain;
    }

    /**
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->urlAccount() . 'oauth/';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->urlAccount() . 'oauth2/access_token';
    }

    /**
     * Get the default scopes used by this provider.
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return [];
    }


    public function urlAccount()
    {
        return $this->protocol . $this->baseDomain . '/';
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return AmoCRMResourceOwner
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new AmoCRMResourceOwner($response);
    }

    /**
     * @inheritDoc
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw AmoCRMException::errorResponse($response, $data);
        }
    }

    /**
     * Get provider url to fetch user details
     *
     * @param AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->urlAccount() . 'v3/user';
    }
    public function saveToken($accessToken, $token_file)
    {
        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            $data = [
                'accessToken' => $accessToken['accessToken'],
                'expires' => $accessToken['expires'],
                'refreshToken' => $accessToken['refreshToken'],
                'baseDomain' => $accessToken['baseDomain'],
            ];

            file_put_contents($token_file, json_encode($data));
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    public function getToken($token_file)
    {
        $accessToken = json_decode(file_get_contents($token_file), true);

        if (
            isset($accessToken)
            && isset($accessToken['accessToken'])
            && isset($accessToken['refreshToken'])
            && isset($accessToken['expires'])
            && isset($accessToken['baseDomain'])
        ) {
            return new \League\OAuth2\Client\Token\AccessToken([
                'access_token' => $accessToken['accessToken'],
                'refresh_token' => $accessToken['refreshToken'],
                'expires' => $accessToken['expires'],
                'baseDomain' => $accessToken['baseDomain'],
            ]);
        } else {
            exit('Invalid access token ' . var_export($accessToken, true));
        }
    }

    public function checkToken($token_file)
    {
        $accessToken = $this->getToken($token_file);
    
        $this->setBaseDomain($accessToken->getValues()['baseDomain']);
        if ($accessToken->hasExpired()) 
        {
            try 
            {
                $accessToken = $this->getAccessToken(new League\OAuth2\Client\Grant\RefreshToken(), [
                'refresh_token' => $accessToken->getRefreshToken(),
                ]);
                $this->saveToken([
                'accessToken' => $accessToken->getToken(),
                'refreshToken' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires(),
                'baseDomain' => $this->getBaseDomain(),
                ], $token_file);

            } catch (Exception $e) 
            {
                die((string)$e);
            }
        }

       return $token = $accessToken->getToken();
    }
}

