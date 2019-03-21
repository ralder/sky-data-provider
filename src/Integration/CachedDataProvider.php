<?php
namespace sky\Integration;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use sky\Infra\LoggerTrait;

class CachedDataProvider implements DataProviderInterface
{
    private const KEY_PREFIX  = 'data_provider';
    use LoggerTrait;

    /** @var CacheItemPoolInterface */
    private $cache;

    /** @var DataProviderInterface */
    private $provider;

    /**
     * @param DataProviderInterface  $provider
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(DataProviderInterface $provider, CacheItemPoolInterface $cache)
    {
        $this->provider = $provider;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function get(array $request): array
    {
        try {
            $cacheKey = $this->getCacheKey($request);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->provider($request);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );

            return $result;
        } catch (Exception $e) {
            $this->getLogger()->critical(
                sprintf('Error on request to data provider: %s', $e->getMessage()),
                ['request' => $request]
            );
        }

        return [];
    }

    private function getCacheKey(array $input): string
    {
        return sprintf('%s-%s', self::KEY_PREFIX, md5(json_encode($input)));
    }
}
