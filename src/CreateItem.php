<?php


namespace DNX\S3;

use Aws\CommandPool;
use Guzzle\Service\Exception\CommandTransferException;
use \Exception;

class CreateItem extends Client
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
     * Upload only one file
     * @param array $file
     */
    public function uploadObject(array $file)
    {
        try {
            $result = $this->s3Client->putObject($file);
            return (object)[ 
                'response'  => 'success', 
                'message'   => 'File Uploaded!', 
                'result'    => $result
            ]; 
        } catch (Exception $exception) {
            return (object)[ 
                'response'  => 'error', 
                'message'   => 'File Not Uploaded!', 
                'result'    => $exception
            ];
        }
    }

    /**
     * Upload multiple files
     * https://docs.aws.amazon.com/aws-sdk-php/v2/guide/feature-commands.html#executing-commands-in-parallel
     * @param string $bucket Bucket Name
     * @param array  $files File List
     */
    public function uploadObjects(string $bucket, array $files)
    {
        $commands   = array();
        foreach ($files as $key => $file) {
            $file['Bucket'] = $bucket;
            $commands[] = $this->s3Client->getCommand('PutObject', $file);    
        }
        try {
            // Execute an array of command objects to do them in parallel    
            $results = CommandPool::batch($this->s3Client, $commands); 
            return (object)[
                'response'  => 'success', 
                'message'   => 'File Batch Finished! Please check each file result in results list.', 
                'results'   => $results
            ];
        } catch (CommandTransferException $e) {
            $succeeded = $e->getSuccessfulCommands();
            $failed    = '';
            foreach ($e->getFailedCommands() as $failedCommand) {
                $failed.= $e->getExceptionForFailedCommand($failedCommand)->getMessage() . "\n";    
            }
            return (object)[ 
                'response'  => 'warning', 
                'message'   => 'Not All Files Uploaded!', 
                'succeeded' => $succeeded,
                'failed'    => $failed
            ];
        }
    }
}
