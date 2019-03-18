<?php
/**
 * HarvestResourceOwner class.
 *
 * @since 1.0.0
 */

namespace Required\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Class used to represent the resource owner authenticated with Harvest.
 *
 * @since 1.0.0
 */
class HarvestResourceOwner implements ResourceOwnerInterface {

	/**
	 * The raw response.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $response;

	/**
	 * Creates new resource owner.
	 *
	 * @since 1.0.0
	 *
	 * @param array $response
	 */
	public function __construct( array $response = [] ) {
		$this->response = $response;
	}

	/**
	 * Returns the identifier of the authorized resource owner.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function getId(): ?string {
		return $this->response['user']['id'] ?: null;
	}

	/**
	 * Returns the email of the authorized resource owner.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function getEmail(): ?string {
		return $this->response['user']['email'] ?: null;
	}

	/**
	 * Returns the name of the authorized resource owner.
	 *
	 * @since 1.0.0
	 *
	 * @return string|null
	 */
	public function getName(): ?string {
		return ( $this->response['user']['first_name'] . ' ' . $this->response['user']['last_name'] ) ?: null;
	}

	/**
	 * Returns the first account data of the authorized resource owner.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getAccount(): array {
		$accounts = $this->getAccounts();
		return reset( $accounts );
	}

	/**
	 * Returns the accounts of the authorized resource owner.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function getAccounts(): array {
		return $this->response['accounts'] ?: [];
	}

	/**
	 * Return all of the owner details available as an array.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function toArray(): array {
		return $this->response;
	}
}
