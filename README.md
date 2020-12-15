# S3 Provider ☁️ 

This library provides a S3 Management.

# Installation

```bash
composer require dnx/php-s3
```

# Usage


## List Bucket
`listObjects($bucket, $path = null)`

```

Required
    $bucket: Bucket name

Optional: 
    $path: File path (if null will list all from s3 root)

Return 2D array e.g.:
    $res->list => [
        'folder1'           => [$files], 
        'folder1/folder3'   => [$files], 
        'folder2'           => [$files]
    ]

```

## Get File 

`getObject($bucket, $filePath)`
```

Required:
    $bucket: Bucket name
    $filePath: 
        File path (if empty will look bucket root) + / +
        File name including extension (image.png)

Return: To download the item, use the `StreamedResponse()` example
        To manipulate the object body, just return `(string)$object->file['Body']`

```

## Get Cloud Front URL

`getCloudFrontURL($bucket, $filePath)`

```
Required:
    $bucket: Bucket name
    $filePath: 
        File path (if empty will look bucket root) + / +
        File name including extension (image.png)

Return: success: The CloudFront URL for the informed bucket and file
        return [ 'code' => 200, 'response' => 'success', 'message' => $url ]

        error: If Cloud Front Distribution not found, it will throw an Exception 
        throw new Exception('CloudFront Distribution Not Found');
```

## Upload Files
`uploadObjects($bucket, $files)`

```

Required:
    $bucket: Bucket name
    $files:  File list following the pattern
             [ 'Key' => 'file path' + 'file name.extension', 'Body' => 'file body' ]

Return:
    success: [ 'response' => 'success', 'message' => 'Files Uploaded!', 'results' => $results];
    warning: [ 'response' => 'warning', 'message' => 'Not All Files Uploaded!', 'succeeded' => $succeeded, 'failed' => $failed ];

```

## Delete File
`deleteObject($bucket, $filePath)`

```

Required:
    $bucket: Bucket name
    $filePath: 
        File path (if empty will look bucket root) + / +
        File name including extension (image.png)
    
Return: 
    success: [ 'response' => 'success', 'message' => $message ];
    error:   throw new Exception($message);


```

## Detailed example of usage

```php

use DNX\S3\CreateItem;
use DNX\S3\ReadItem;
use DNX\S3\DeleteItem;

use Aws\Credentials\CredentialProvider;
use Aws\S3\Exception\S3Exception;
use \Exception;
use Log;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct(){}


    public function listObjects(Request $request)
    {
        try {
            $query  = (object)$request->all();
            // CredentialProvider is optional, we recommend to use user roles and do not use credentials
            $readS3 = new ReadItem(CredentialProvider::env());
            $res    = $readS3->listObjects($query->bucket, $query->path);
            return view('s3.list', [ 'list' => $res->list, 'bucket' => $bucket]);
        } catch(S3Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getAwsErrorMessage(), 'code' => $e->getStatusCode() ]; 
        } catch(Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getMessage() ];
        }
    }
    
    public function getObject(Request $request)
    {
        try {
            $query  = (object)$request->all();
            // CredentialProvider is optional, we recommend to use user roles and do not use credentials
            $readS3 = new ReadItem(CredentialProvider::env());
            $object = $readS3->getObject($query->bucket, $query->filePath);
            $headers = [
                'Content-Type'        => $object->file['ContentType'],
                'Content-Disposition' => 'attachment; filename=' . $query->file
            ];
            $stream = function () use ($object) {
                echo $object->file['Body'];
            };
            return new StreamedResponse($stream, 200, $headers);    // If you want to download it
            // return (string)$object->file['Body'];                // If you want to manipulate file body
        } catch(S3Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getAwsErrorMessage(), 'code' => $e->getStatusCode() ];
        } catch(Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getMessage() ];
        }
    }

    public function getCloudFrontURL(Request $request)
    {
        try {
            $query      = (object)$request->all();
            // CredentialProvider is optional, we recommend to use user roles and do not use credentials
            $readS3     = new ReadItem(CredentialProvider::env());
            $url        = $readS3->getCloudFrontURL($query->bucket, $query->filePath);
            return $url;
        } catch(S3Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getAwsErrorMessage(), 'code' => $e->getStatusCode() ];
        } catch(Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getMessage() ];
        }
    }

    public function uploadObjects(Request $request)
    {
        // https://docs.aws.amazon.com/aws-sdk-php/v2/guide/feature-commands.html#executing-commands-in-parallel
        // Without ContentType parameter the file will be downloaded instead of served
        try {
            $query      = (object)$request->all();
            $files      = [];
            foreach ($query->files as $key => $file ) {
                $key = (isset($query->path) ? $query->path . "/" : "") . $file->getClientOriginalName();
                array_push($files, [
                    'Key'           => $key,     
                    'Body'          => $file->get(),
                    'ContentType'   => $file->getClientMimeType() 
                ]);
            }
            // CredentialProvider is optional, we recommend to use user roles and do not use credentials
            $createS3   = new CreateItem(CredentialProvider::env());
            $res        = $createS3->uploadObjects($query->bucket, $files);
            foreach($res->results as $index => $result)
            {
                if($result instanceof Exception) {
                    $message   = $result->getMessage();
                    Log::error("Error on creating file: {$message}");
                } else {
                    // Database persist for each file here
                    Log::info("Creating file completed");
                }
            }
            return $res;
        } catch (Exception $e) {
            $message   = $e->getMessage();
            Log::error("S3Client Bulk Load Error: {$message}");
        }
    }

    public function deleteObject(Request $request)
    {
        try {
            $query      = (object)$request->all();
            // CredentialProvider is optional, we recommend to use user roles and do not use credentials
            $deleteS3   = new DeleteItem(CredentialProvider::env());
            $res        = $deleteS3->deleteObject($query->bucket, $query->filePath);
            return $res;
        } catch(S3Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getAwsErrorMessage(), 'code' => $e->getStatusCode() ];
        } catch(Exception $e) {
            return [ 'response' => 'error', 'message' => $e->getMessage() ];
        }
    }
}
```