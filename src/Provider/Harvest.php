<?php
/**
 * Harvest class.
 *
 * @since 1.0.0
 */

namespace Required\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

/**
 * Class used to represents the authorization server of Harvest.
 *
 * @since 1.0.0
 */
class Harvest extends AbstractProvider {

	/**
	 * Returns the base URL for authorizing a client.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl() {
		return 'https://id.getharvest.com/oauth2/authorize';
	}

	/**
	 * Returns the base URL for requesting an access token.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params
	 * @return string
	 */
	public function getBaseAccessTokenUrl( array $params ): string  {
		return 'https://id.getharvest.com/api/v2/oauth2/token';
	}

	/**
	 * Returns the URL for requesting the resource owner's details.
	 *
	 * @since 1.0.0
	 *
	 * @param AccessToken $token
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl( AccessToken $token ): string {
		return 'https://id.getharvest.com/api/v2/accounts';
	}

	/**
	 * Returns the default scopes used by this provider.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function getDefaultScopes(): array {
		return [];
	}

	/**
	 * Returns authorization headers for the 'bearer' grant.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed|null $token Either a string or an access token instance
	 * @return array
	 */
	protected function getAuthorizationHeaders( $token = null ): array {
		return [
			'Authorization' => 'Bearer ' . $token,
			'Content-Type'  => 'application/json',
			'Accept'        => 'application/json',
		];
	}

	/**
	 * Checks a provider response for errors.
	 *
	 * @since 1.0.0
	 *
	 * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
	 *
	 * @param \Psr\Http\Message\ResponseInterface $response
	 * @param array|string                        $data Parsed response data
	 */
	protected function checkResponse( ResponseInterface $response, $data ) {
		if ( isset( $data['error_description'] ) ) {
			throw new IdentityProviderException( $data['error_description'], $response->getStatusCode(), $response->getBody() );
		}
	}

	/**
	 * Generates a resource owner object from a successful resource owner
	 * details request.
	 *
	 * @since 1.0.0
	 *
	 * @param  array       $response
	 * @param  AccessToken $token
	 * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
	 */
	protected function createResourceOwner( array $response, AccessToken $token ) {
		return new OAuth2ResourceOwner( $response );
	}

	/**
	 * Requests an access token using a specified grant and option set.
	 *
	 * @since 1.0.0
	 *
	 * @throws \UnexpectedValueException
	 *
	 * @param  mixed $grant
	 * @param  array $options
	 * @return \League\OAuth2\Client\Token\AccessToken
	 */
	public function getAccessToken( $grant, array $options = [] ) {
		$grant = $this->verifyGrant( $grant );

		$params = [
			'client_id'     => $this->clientId,
			'client_secret' => $this->clientSecret,
			'redirect_uri'  => $this->redirectUri,
		];

		$params   = $grant->prepareRequestParameters( $params, $options );
		$request  = $this->getAccessTokenRequest( $params );
		$response = $this->getParsedResponse( $request );

		if ( ! is_array( $response ) ) {
			throw new \UnexpectedValueException( 'Parsed response is not of type array.' );
		}

		$prepared = $this->prepareAccessTokenResponse( $response );
		$token    = $this->createAccessToken( $prepared, $grant );

		return $token;
	}
}
