<?php
namespace Drupal\marklogic_integration\Service;

use Drupal\Core\Site\Settings;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Provides communication with MarkLogic REST API.
 */
class MarkLogicClient {

  protected ClientInterface $httpClient;
  protected array $config;

  public function __construct(ClientInterface $http_client) {
    $this->httpClient = $http_client;
    $this->config = Settings::get('marklogic', []);
  }

  /**
   * Inserts or updates a JSON document.
   */
  public function saveDocument(string $uri, array $document): array {
    return $this->request('PUT', '/v1/documents', [
      'query' => ['uri' => $uri],
      'json' => $document,
    ]);
  }

  /**
   * Retrieves a document.
   */
  public function getDocument(string $uri): array {
    return $this->request('GET', '/v1/documents', [
      'query' => ['uri' => $uri],
    ]);
  }

  /**
   * Deletes a document.
   */
  public function deleteDocument(string $uri): array {
    return $this->request('DELETE', '/v1/documents', [
      'query' => ['uri' => $uri],
    ]);
  }

  /**
   * Sends an authenticated request to MarkLogic.
   */
  protected function request(
    string $method,
    string $endpoint,
    array $options = []
  ): array {
    if (empty($this->config['base_url']) ||
        empty($this->config['username']) ||
        !isset($this->config['password'])) {
      throw new \RuntimeException('MarkLogic configuration is incomplete.');
    }

    $options['auth'] = [
      $this->config['username'],
      $this->config['password'],
      'digest',
    ];

    $options['http_errors'] = FALSE;

    $response = $this->httpClient->request(
      $method,
      rtrim($this->config['base_url'], '/') . $endpoint,
      $options
    );

    $status = $response->getStatusCode();
    $body = (string) $response->getBody();

    if ($status < 200 || $status >= 300) {
      throw new \RuntimeException(
        "MarkLogic request failed. HTTP status: $status. Response: $body"
      );
    }

    return [
      'status' => $status,
      'body' => $body !== '' ? json_decode($body, TRUE) : NULL,
    ];
  }

}
