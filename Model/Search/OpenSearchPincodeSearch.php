<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Model\Search;

use OpenSearch\ClientBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class OpenSearchPincodeSearch
 * @package Domus\CustomerDeliveryChecker\Model\Search
 */
class OpenSearchPincodeSearch
{
    /**
     * OpenSearch index name
     */
    const INDEX_NAME = 'domus_delivery_pincodes';
    
    /**
     * @var ClientBuilder
     */
    private ClientBuilder $clientBuilder;
    
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    
    /**
     * @var \OpenSearch\Client|null
     */
    private ?\OpenSearch\Client $client = null;

    /**
     * OpenSearchPincodeSearch constructor.
     *
     * @param ClientBuilder $clientBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientBuilder $clientBuilder,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->clientBuilder = $clientBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * Get OpenSearch client
     *
     * @return \OpenSearch\Client|null
     */
    private function getClient(): ?\OpenSearch\Client
    {
        if ($this->client === null) {
            try {
                $hosts = $this->getOpenSearchHosts();
                $this->client = $this->clientBuilder
                    ->setHosts($hosts)
                    ->setSSLVerification($this->isSSLEnabled())
                    ->setBasicAuthentication(
                        $this->getUsername(),
                        $this->getPassword()
                    )
                    ->build();
            } catch (\Exception $e) {
                $this->logger->error('Failed to initialize OpenSearch client: ' . $e->getMessage());
                return null;
            }
        }
        
        return $this->client;
    }

    /**
     * Create or update index
     *
     * @return bool
     */
    public function createIndex(): bool
    {
        $client = $this->getClient();
        
        if (!$client) {
            return false;
        }

        $indexParams = [
            'index' => self::INDEX_NAME,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'pincode_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'keyword',
                                'filter' => ['lowercase', 'phonetic', 'autocomplete']
                            ]
                        ],
                        'filter' => [
                            'phonetic' => [
                                'type' => 'phonetic',
                                'encoder' => 'metaphone'
                            ],
                            'autocomplete' => [
                                'type' => 'edge_ngram',
                                'min_gram' => 2,
                                'max_gram' => 10
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'pincode' => [
                            'type' => 'keyword',
                            'analyzer' => 'pincode_analyzer'
                        ],
                        'city' => [
                            'type' => 'text',
                            'fields' => [
                                'suggest' => [
                                    'type' => 'completion',
                                    'analyzer' => 'simple'
                                ],
                                'keyword' => [
                                    'type' => 'keyword'
                                ]
                            ],
                            'analyzer' => 'standard'
                        ],
                        'state' => [
                            'type' => 'keyword'
                        ],
                        'country' => [
                            'type' => 'keyword'
                        ],
                        'estimated_days' => [
                            'type' => 'integer'
                        ],
                        'cod_available' => [
                            'type' => 'boolean'
                        ],
                        'is_active' => [
                            'type' => 'boolean'
                        ],
                        'latitude' => [
                            'type' => 'float'
                        ],
                        'longitude' => [
                            'type' => 'float'
                        ],
                        'location' => [
                            'type' => 'geo_point'
                        ],
                        'created_at' => [
                            'type' => 'date'
                        ],
                        'updated_at' => [
                            'type' => 'date'
                        ]
                    ]
                ]
            ]
        ];

        try {
            // Delete index if exists
            if ($client->indices()->exists(['index' => self::INDEX_NAME])) {
                $client->indices()->delete(['index' => self::INDEX_NAME]);
            }
            
            // Create new index
            $response = $client->indices()->create($indexParams);
            return $response['acknowledged'];
        } catch (\Exception $e) {
            $this->logger->error('Failed to create OpenSearch index: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Index pincode data
     *
     * @param array $pincodeData
     * @return bool
     */
    public function indexPincode(array $pincodeData): bool
    {
        $client = $this->getClient();
        
        if (!$client) {
            return false;
        }

        $params = [
            'index' => self::INDEX_NAME,
            'id' => $pincodeData['entity_id'],
            'body' => $this->prepareDocument($pincodeData)
        ];

        try {
            $response = $client->index($params);
            return $response['result'] === 'created' || $response['result'] === 'updated';
        } catch (\Exception $e) {
            $this->logger->error('Failed to index pincode: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk index pincodes
     *
     * @param array $pincodeDataArray
     * @return array
     */
    public function bulkIndex(array $pincodeDataArray): array
    {
        $client = $this->getClient();
        
        if (!$client) {
            return ['success' => false, 'message' => 'OpenSearch client not available'];
        }

        $params = ['body' => []];
        
        foreach ($pincodeDataArray as $pincodeData) {
            $params['body'][] = [
                'index' => [
                    '_index' => self::INDEX_NAME,
                    '_id' => $pincodeData['entity_id']
                ]
            ];
            
            $params['body'][] = $this->prepareDocument($pincodeData);
        }

        try {
            $response = $client->bulk($params);
            
            // Check for errors
            $errors = [];
            foreach ($response['items'] as $item) {
                if (isset($item['index']['error'])) {
                    $errors[] = $item['index']['error'];
                }
            }
            
            return [
                'success' => empty($errors),
                'indexed' => count($pincodeDataArray) - count($errors),
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            $this->logger->error('Bulk index failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Search pincodes
     *
     * @param string $query
     * @param array $filters
     * @param int $size
     * @param int $from
     * @return array
     */
    public function search(string $query, array $filters = [], int $size = 10, int $from = 0): array
    {
        $client = $this->getClient();
        
        if (!$client) {
            return [];
        }

        $searchParams = [
            'index' => self::INDEX_NAME,
            'body' => [
                'size' => $size,
                'from' => $from,
                'query' => $this->buildQuery($query, $filters),
                'highlight' => [
                    'fields' => [
                        'city' => [],
                        'pincode' => []
                    ]
                ],
                'sort' => [
                    '_score' => ['order' => 'desc'],
                    'pincode' => ['order' => 'asc']
                ]
            ]
        ];

        try {
            $response = $client->search($searchParams);
            return $this->formatSearchResults($response);
        } catch (\Exception $e) {
            $this->logger->error('Search failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Autocomplete suggestions
     *
     * @param string $text
     * @param array $filters
     * @return array
     */
    public function autocomplete(string $text, array $filters = []): array
    {
        $client = $this->getClient();
        
        if (!$client) {
            return [];
        }

        $searchParams = [
            'index' => self::INDEX_NAME,
            'body' => [
                'suggest' => [
                    'city_suggest' => [
                        'prefix' => $text,
                        'completion' => [
                            'field' => 'city.suggest',
                            'size' => 10,
                            'skip_duplicates' => true
                        ]
                    ],
                    'pincode_suggest' => [
                        'prefix' => $text,
                        'completion' => [
                            'field' => 'pincode',
                            'size' => 10,
                            'skip_duplicates' => true
                        ]
                    ]
                ]
            ]
        ];

        if (!empty($filters)) {
            $searchParams['body']['suggest']['city_suggest']['completion']['contexts'] = $filters;
            $searchParams['body']['suggest']['pincode_suggest']['completion']['contexts'] = $filters;
        }

        try {
            $response = $client->search($searchParams);
            return $this->formatAutocompleteResults($response);
        } catch (\Exception $e) {
            $this->logger->error('Autocomplete failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Geospatial search
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $distance
     * @param array $filters
     * @return array
     */
    public function geoSearch(float $latitude, float $longitude, int $distance = 50, array $filters = []): array
    {
        $client = $this->getClient();
        
        if (!$client) {
            return [];
        }

        $searchParams = [
            'index' => self::INDEX_NAME,
            'body' => [
                'size' => 20,
                'query' => [
                    'bool' => [
                        'must' => $this->buildFilters($filters),
                        'filter' => [
                            'geo_distance' => [
                                'distance' => $distance . 'km',
                                'location' => [
                                    'lat' => $latitude,
                                    'lon' => $longitude
                                ]
                            ]
                        ]
                    ]
                ],
                'sort' => [
                    '_geo_distance' => [
                        'location' => [
                            'lat' => $latitude,
                            'lon' => $longitude
                        ],
                        'order' => 'asc',
                        'unit' => 'km'
                    ]
                ]
            ]
        ];

        try {
            $response = $client->search($searchParams);
            return $this->formatSearchResults($response);
        } catch (\Exception $e) {
            $this->logger->error('Geo search failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete pincode from index
     *
     * @param int $pincodeId
     * @return bool
     */
    public function deletePincode(int $pincodeId): bool
    {
        $client = $this->getClient();
        
        if (!$client) {
            return false;
        }

        try {
            $response = $client->delete([
                'index' => self::INDEX_NAME,
                'id' => $pincodeId
            ]);
            return $response['result'] === 'deleted';
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete pincode: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Prepare document for indexing
     *
     * @param array $pincodeData
     * @return array
     */
    private function prepareDocument(array $pincodeData): array
    {
        $document = $pincodeData;
        
        // Add geo_point if coordinates are available
        if (isset($pincodeData['latitude']) && isset($pincodeData['longitude'])) {
            $document['location'] = [
                'lat' => $pincodeData['latitude'],
                'lon' => $pincodeData['longitude']
            ];
        }
        
        return $document;
    }

    /**
     * Build search query
     *
     * @param string $query
     * @param array $filters
     * @return array
     */
    private function buildQuery(string $query, array $filters): array
    {
        $mustClauses = [];
        
        if (!empty($query)) {
            $mustClauses[] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => [
                        'city^3',      // Higher weight for city
                        'pincode^2',   // Medium weight for pincode
                        'state'        // Lower weight for state
                    ],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO'
                ]
            ];
        }
        
        $filterClauses = $this->buildFilters($filters);
        
        if (!empty($mustClauses) || !empty($filterClauses)) {
            return [
                'bool' => [
                    'must' => $mustClauses,
                    'filter' => $filterClauses
                ]
            ];
        }
        
        return ['match_all' => new \stdClass()];
    }

    /**
     * Build filter clauses
     *
     * @param array $filters
     * @return array
     */
    private function buildFilters(array $filters): array
    {
        $filterClauses = [];
        
        if (isset($filters['is_active'])) {
            $filterClauses[] = ['term' => ['is_active' => $filters['is_active']]];
        }
        
        if (isset($filters['state'])) {
            $filterClauses[] = ['term' => ['state' => $filters['state']]];
        }
        
        if (isset($filters['cod_available'])) {
            $filterClauses[] = ['term' => ['cod_available' => $filters['cod_available']]];
        }
        
        if (isset($filters['max_estimated_days'])) {
            $filterClauses[] = ['range' => ['estimated_days' => ['lte' => $filters['max_estimated_days']]]];
        }
        
        return $filterClauses;
    }

    /**
     * Format search results
     *
     * @param array $response
     * @return array
     */
    private function formatSearchResults(array $response): array
    {
        $results = [];
        
        foreach ($response['hits']['hits'] as $hit) {
            $result = $hit['_source'];
            $result['_score'] = $hit['_score'];
            $result['_id'] = $hit['_id'];
            
            if (isset($hit['highlight'])) {
                $result['_highlight'] = $hit['highlight'];
            }
            
            if (isset($hit['sort'])) {
                $result['_distance'] = $hit['sort'][0] ?? null;
            }
            
            $results[] = $result;
        }
        
        return [
            'results' => $results,
            'total' => $response['hits']['total']['value'],
            'max_score' => $response['hits']['max_score']
        ];
    }

    /**
     * Format autocomplete results
     *
     * @param array $response
     * @return array
     */
    private function formatAutocompleteResults(array $response): array
    {
        $suggestions = [];
        
        foreach ($response['suggest'] as $suggestType => $suggestionGroup) {
            foreach ($suggestionGroup as $suggestionsData) {
                foreach ($suggestionsData['options'] as $option) {
                    $suggestions[] = [
                        'text' => $option['text'],
                        'source' => $option['_source'] ?? null,
                        'score' => $option['_score'],
                        'type' => $suggestType
                    ];
                }
            }
        }
        
        // Sort by score
        usort($suggestions, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        
        return array_slice($suggestions, 0, 10);
    }

    // Configuration helper methods
    private function getOpenSearchHosts(): array
    {
        $hosts = $this->scopeConfig->getValue(
            'customer_delivery_checker/opensearch/hosts',
            ScopeInterface::SCOPE_STORE
        );
        
        return $hosts ? explode(',', $hosts) : ['localhost:9200'];
    }

    private function isSSLEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            'customer_delivery_checker/opensearch/ssl_enabled',
            ScopeInterface::SCOPE_STORE
        );
    }

    private function getUsername(): string
    {
        return $this->scopeConfig->getValue(
            'customer_delivery_checker/opensearch/username',
            ScopeInterface::SCOPE_STORE
        ) ?: '';
    }

    private function getPassword(): string
    {
        return $this->scopeConfig->getValue(
            'customer_delivery_checker/opensearch/password',
            ScopeInterface::SCOPE_STORE
        ) ?: '';
    }
}
