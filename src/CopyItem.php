<?php

namespace DNX\S3;

class CopyItem extends Client
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($provider = null)
    {
        parent::__construct($provider);
    }

    /**
     * @param string $sourceBucket Name of the bucket the object is located inside
     * @param string $sourceFilePath Name of the object you want to copy (including the path)
     * @param string $targetBucket Name of the bucket you want to move the object to
     * @param string $targetFilePath Name of the copied object (including the path)
     * @return object Success if the object is successfully copied
     */
    public function copyObject(string $sourceBucket, string $sourceFilePath, string $targetBucket, string $targetFilePath)
    {
        $params = array_merge([
            'Bucket' => $targetBucket,
            'Key' => $targetFilePath,
            'CopySource' => "{$sourceBucket}/{$sourceFilePath}"
            ]
        );
        $s3Object = $this->s3Client->copyObject($params);
        return (object)['response' => 'success', 'file' => $s3Object];
    }

}