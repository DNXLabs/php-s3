<?php

namespace DNX\S3;

use Aws\S3\S3Client;
use Aws\CloudFront\CloudFrontClient;

class Client
{
    /**
     * @var S3ClientInterface
     */
    protected $s3Client;
    protected $cloudFrontClient;
    private $region;

    public function __construct($provider = null)
    {
        // If no region is provided we shall default to Sydney
        $this->region = $this->region ?: Region::sydney();
        $params = [
            'region'  => $this->region->code(),
            'version' => 'latest'
        ];
        if(isset($provider)) {
            $params['credentials'] = $provider;
        }
        $this->s3Client         = new S3Client($params);
        $this->cloudFrontClient = new CloudFrontClient($params);
    }
}
