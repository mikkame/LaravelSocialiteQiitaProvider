<?php
namespace Mikkame\QiitaSocialiteProvider;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;
use GuzzleHttp\ClientInterface;

class QiitaProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://qiita.com/api/v2/oauth/authorize', $state);
    }
    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://qiita.com/api/v2/access_tokens';
    }
    /**
     * {@inheritdoc}
     */
    public function getAccessToken($code)
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => ['Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)],
            'body'    => $this->getTokenFields($code),
        ]);
        return $this->parseAccessToken($response->getBody());
    }
    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_add(
            parent::getTokenFields($code),
            'grant_type',
            'authorization_code'
        );
    }
    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://qiita.com/api/v2/authenticated_user', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
    /**
     * {@inheritdoc}
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        $scopes[] = 'read_qiita';
        array_unique($scopes);
        return implode($scopeSeparator, $scopes);
    }
    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {

        return (new User)->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['id'],
            'name'     => $user['id'],
            'avatar'   => !empty($user['profile_image_url']) ? $user['profile_image_url'] : null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this->hasInvalidState()) {
            throw new InvalidStateException;
        }
        $response = $this->getAccessTokenResponse($this->getCode());

        $user     = $this->mapUserToObject(
            $this->getUserByToken($token = Arr::get($response, 'token'))
        );
        return $user->setToken($token);
    }
    /**
     * Get the access_token, refresh_token (optional) and expires_in values.
     *
     * @param  string  $code
     *
     * @return array
     */
    public function getAccessTokenResponse($code)
    {

        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            \GuzzleHttp\RequestOptions::JSON  => $this->getTokenFields($code),
        ]);
        return json_decode($response->getBody(), true);
    }
}
