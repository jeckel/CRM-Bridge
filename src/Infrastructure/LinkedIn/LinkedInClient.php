<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/01/2024 21:13
 */
declare(strict_types=1);

namespace App\Infrastructure\LinkedIn;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class LinkedInClient
{
    public function __construct(
        private readonly string $client_id,
        private readonly string $client_secret,
        private readonly string $redirect_uri,
        private readonly Security $security,
        private readonly UserRepository $userRepository
    ) {
    }

    public function getRedirectionUri(): string
    {
        $csrf_token = random_int(1111111, 9999999);
        $_SESSION['csrf_token'] = $csrf_token;
        // $scope = 'r_liteprofile r_emailaddress w_member_social';
        $scope = 'w_member_social openid profile email';

        $authorizationUrl = "https://www.linkedin.com/oauth/v2/authorization";
        $params = [
            'response_type' => 'code',
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'state' => $csrf_token,
            'scope' => $scope
        ];

        $url = $authorizationUrl . '?' . http_build_query($params);
        return $url;
    }

    public function exchangeCodeToAccessToken(string $code): string
    {
        $token_url = 'https://www.linkedin.com/oauth/v2/accessToken';
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirect_uri,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $token_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        if (isset($response->error)) {
            dd($response);
        }
//        dd(json_decode($response));
        $access_token = $response->access_token;

        $user = $this->security->getUser();
        $user->setLinkedInAccessToken($access_token);
        $this->userRepository->save($user);
        return $access_token;
    }
}
