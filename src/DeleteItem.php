<?php


namespace DNX\S3;

use Aws\ResultInterface;
use \Exception;

class DeleteItem extends Client
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
     * Delete an item.
     * https://docs.aws.amazon.com/cli/latest/reference/s3api/delete-object.html
     *
     * @param string $bucket    Bucket name
     * @param string $filePath  File path including file name
     *
     * @return object
     * @throws \Exception
     */
    public function deleteObject(string $bucket, string $filePath)
    {
        $config = [ 'Bucket' => $bucket, 'Key' => $filePath ];

        $delete = $this->s3Client->deleteObject($config);
        if (($delete instanceof ResultInterface) and $delete['@metadata']['statusCode'] === 204) {
            $message = sprintf('File \'%s\' was successfully deleted from \'%s\' bucket', $filePath, $bucket);
            return (object)[ 
                'response'  => 'success', 
                'message'   => $message
            ];
        }
        $message = sprintf('Something went wrong in deleting file \'%s\' from \'%s\' bucket', $filePath, $bucket);
        throw new Exception($message);
    }
}
