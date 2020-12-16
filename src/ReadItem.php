<?php

namespace DNX\S3;
use \Exception;

class ReadItem extends Client
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(string $region, $provider = null)
    {
        parent::__construct($region, $provider);
    }

    /**
     * Get one object
     * https://docs.aws.amazon.com/cli/latest/reference/s3api/delete-object.html
     *
     * @param string $bucket    Bucket name
     * @param string $filePath  File path including file name
     *
     * @return object
     */
    public function getObject(string $bucket, string $filePath)
    {
        $params = array_merge([
            'Bucket' => $bucket,
            'Key'    => $filePath
        ]);
        $s3Object = $this->s3Client->getObject($params);
        return (object)['response' => 'success', 'file' => $s3Object ];
    }

    /**
     * Get list of objects in a bucket or bucket path
     * https://docs.aws.amazon.com/cli/latest/reference/s3api/delete-object.html
     *
     * @param string $bucket    Bucket name
     * @param string $path      File path not including file name
     *
     * @return object
     */
    public function listObjects(string $bucket, string $path = null)
    {
        $params = [
            'Bucket' => $bucket,
            'Prefix' => isset($path) ? $path . "/" : ""
        ];
        // Use the high-level iterators (returns ALL of your objects).
        $results= $this->s3Client->getPaginator('ListObjects', $params);
        $list   = [];
        foreach ($results as $result) {
            if(isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $file = explode("/", $object['Key']);
                    $path = implode("/", explode('/', $object['Key'], -1));
                    if(!isset($list[$path])){
                        $list[$path] = [];
                    }
                    if(end($file) != '') {
                        array_push($list[$path], end($file));
                    }
                }
            }
        }
        return (object)[                                               
            'code'      => 200,
            'response'  => 'success',
            'list'      => $list
        ];
    }

    /**
     * Returns CloudFront URL from bucket name
     *
     * @param string $bucket    Bucket name
     * @param string $filePath  File path including file name
     *
     * @return object
     * @throws \Exception if Distribution not found
     */
    public function getCloudFrontURL(string $bucket, string $filePath)
    {
        $s3URL      = $this->s3Client->getObjectUrl($bucket, $filePath);                    // Get S3 URL
        $result     = $this->cloudFrontClient->listDistributions([]);                       // List Cloud Front Distributions
        $s3Domain   = explode("/", str_replace("https://", "", $s3URL))[0];                 // Isolate S3 Domain
        if (count($result) > 0 && isset($result['DistributionList']['Items'])) {
            foreach ($result['DistributionList']['Items'] as $distribution) {
                $origin = $distribution['DefaultCacheBehavior']['TargetOriginId'];          // Target can be equal S3 name or s3Origin
                $coment = $distribution['Comment'];                                         // Comment can be S3 name 
                $domain = $distribution['Origins']['Items'][0]['DomainName'];               // Origin Domain Name include S3 name

                switch(true) {
                    case $origin == 'S3-'. $bucket:
                    case $origin == 's3Origin' && $coment == $bucket:
                    case explode(".", $domain)[0] == $bucket:
                        $alias = isset($distribution['Aliases']['Items']) ? 
                                     $distribution['Aliases']['Items'][0] :                 // Alternate Domain Names (CNAMEs)
                                     $distribution['DomainName'];                           // Cloud Front Domain Name
                        $url = str_replace($s3Domain, $alias, $s3URL);                      // Replace S3 to Cloud Front Domain
                        return (object)[                                                    // Return File URL
                            'code'      => 200,
                            'response'  => 'success',
                            'message'   => $url
                        ]; 
                }
            }
        }
        throw new Exception('CloudFront Distribution Not Found');
    }

}