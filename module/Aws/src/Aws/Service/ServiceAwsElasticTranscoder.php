<?php

namespace Aws\Service;

use Zend\Mvc\Controller\AbstractActionController;

class ServiceAmazonElasticTranscoder extends AbstractActionController
{
  protected $_debug = true;

  public function getVideoMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('VideoMapper');
  }

	public function transcode($video)
	{
    $config = $this->getServiceLocator()->get('Config');

    // $ext = pathinfo($video->getFilename(), PATHINFO_EXTENSION);
    $ext = 'mp4';

    try {
      $elasticTranscoderClient = new \Aws\ElasticTranscoder\ElasticTranscoderClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
      ]);
      // print_r($elasticTranscoderClient);

      $result = $elasticTranscoderClient->createJob(array(
        'PipelineId' => $config['amazonApi']['Elastic Transcoder']['Pipeline Id'],
        'Input' => array(
          'Key' => $video->getId() . "/video." . $ext,
          'FrameRate' => 'auto',
          'Resolution' => 'auto',
          'AspectRatio' => 'auto',
          'Interlaced' => 'auto',
          'Container' => 'auto',
        ),
        'Outputs' => array(
          array(
            'Key' => 'mpeg-dash4-8',
            'ThumbnailPattern' => 'thumbnail-{count}',
            'Rotate' => 'auto',
            'SegmentDuration' => '10',
            'PresetId' => $config['amazonApi']['Elastic Transcoder']['MPEG-Dash Video - 4.8M'],
            'Watermarks' => array(
              array(
                'PresetWatermarkId' => 'TopRight',
                'InputKey' => 'watermark/watermark.png',
              ),
            ),
            'Captions' => array(
              'MergePolicy' => 'MergeOverride',
              'CaptionSources' => array(
                array(
                  'Key' => $video->getId() . "/subtitle.srt",
                  'Language' => 'en',
                  'TimeOffset' => '00:00:00',
                  'Label' => 'English',
                ),
              ),
              'CaptionFormats' => array(
                array(
                  'Format' => 'webvtt',
                  'Pattern' => 'subtitle-{language}',
                ),
              ),
            ),
          ),
          array(
            'Key' => 'mpeg-dash2-4',
            'Rotate' => 'auto',
            'SegmentDuration' => '10',
            'PresetId' => $config['amazonApi']['Elastic Transcoder']['MPEG-Dash Video - 2.4M'],
            'Captions' => array(
              'MergePolicy' => 'MergeOverride',
              'CaptionSources' => array(
                array(
                  'Key' => $video->getId() . "/subtitle.srt",
                  'Language' => 'en',
                  'TimeOffset' => '00:00:00',
                  'Label' => 'English',
                ),
              ),
              'CaptionFormats' => array(
                array(
                  'Format' => 'webvtt',
                  'Pattern' => 'subtitle-{language}',
                ),
              ),
            ),
          ),
          array(
            'Key' => 'mpeg-dash1-2',
            'Rotate' => 'auto',
            'SegmentDuration' => '10',
            'PresetId' => $config['amazonApi']['Elastic Transcoder']['MPEG-Dash Video - 1.2M'],
            'Captions' => array(
              'MergePolicy' => 'MergeOverride',
              'CaptionSources' => array(
                array(
                  'Key' => $video->getId() . "/subtitle.srt",
                  'Language' => 'en',
                  'TimeOffset' => '00:00:00',
                  'Label' => 'English',
                ),
              ),
              'CaptionFormats' => array(
                array(
                  'Format' => 'webvtt',
                  'Pattern' => 'subtitle-{language}',
                ),
              ),
            ),
          ),
          array(
            'Key' => 'mpeg-dash600k',
            'Rotate' => 'auto',
            'SegmentDuration' => '10',
            'PresetId' => $config['amazonApi']['Elastic Transcoder']['MPEG-Dash Video - 600k'],
            'Captions' => array(
              'MergePolicy' => 'MergeOverride',
              'CaptionSources' => array(
                array(
                  'Key' => $video->getId() . "/subtitle.srt",
                  'Language' => 'en',
                  'TimeOffset' => '00:00:00',
                  'Label' => 'English',
                ),
              ),
              'CaptionFormats' => array(
                array(
                  'Format' => 'webvtt',
                  'Pattern' => 'subtitle-{language}',
                ),
              ),
            ),
          ),
          array(
            'Key' => 'mpeg-dash128k',
            'SegmentDuration' => '10',
            'PresetId' => $config['amazonApi']['Elastic Transcoder']['MPEG-Dash Audio - 128k'],
            'Captions' => array(
              'MergePolicy' => 'MergeOverride',
              'CaptionSources' => array(
                array(
                  'Key' => $video->getId() . "/subtitle.srt",
                  'Language' => 'en',
                  'TimeOffset' => '00:00:00',
                  'Label' => 'English',
                ),
              ),
              'CaptionFormats' => array(
                array(
                  'Format' => 'webvtt',
                  'Pattern' => 'subtitle-{language}',
                ),
              ),
            ),
          ),
        ),
        'OutputKeyPrefix' => $video->getId() . "/",
        'Playlists' => array(
          array(
            'Name' => "dash",
            'Format' => 'MPEG-DASH',
            'OutputKeys' => array(
              'mpeg-dash4-8',
              'mpeg-dash2-4',
              'mpeg-dash1-2',
              'mpeg-dash600k',
              'mpeg-dash128k',
            ),
          ),
        ),
        'UserMetadata' => array(
          'video_id' => $video->getId(),
        ),
      ));

      // print_r($result);
      $video->setAwsEtResult(serialize($result));
      $this->getVideoMapper()->saveVideo($video);
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
	}

	/*
	 * http://docs.aws.amazon.com/aws-sdk-php/v2/api/class-Aws.ElasticTranscoder.ElasticTranscoderClient.html#_readJob
	 */
	public function checkImageJobs()
	{
    $config = $this->getServiceLocator()->get('Config');

    $filter = array(
      'is_aws_job_processed' => 'N',
    );
    $order = array(
      'created_datetime',
    );
    $videos = $this->getVideoMapper()->fetchAll(false, $filter, $order);
    if(count($videos) > 0){
      foreach ($videos as $video){
        if($this->_debug){
          echo $video->getId();
        }

        $awsEtResult = unserialize($video->getAwsEtResult());
        // print_r($awsEtResult);
        $jobId = isset($awsEtResult['Job']['Id']) ? $awsEtResult['Job']['Id'] : null;
        if(!is_null($jobId)){
          if($this->_debug){
            echo  "|" . $jobId;
          }

          try {
            $elasticTranscoderClient = new \Aws\ElasticTranscoder\ElasticTranscoderClient([
              'version'     => 'latest',
              'region'      => $config['amazonApi']['Region'],
              'credentials' => [
                'key'    => $config['amazonApi']['Access key ID'],
                'secret' => $config['amazonApi']['Secret access key']
              ]
            ]);
            // print_r($elasticTranscoderClient);

            $result = $elasticTranscoderClient->readJob(array(
              'Id' => $jobId,
            ));
            // print_r($result);
            $status = isset($result['Job']['Status']) ? $result['Job']['Status'] : null;
            if($this->_debug){
              echo  "|" . $status;
            }
            if($status == 'Complete'){
              // Image Detection

              $serviceAmazonImageRekognition = $this->getServiceLocator()->get('ServiceAmazonImageRekognition');
              $serviceAmazonImageRekognition->getDetectLabel($video);
              $serviceAmazonImageRekognition->getDetectFaces($video);
              $serviceAmazonImageRekognition->getDetectText($video);
              $serviceAmazonImageRekognition->getDetectModerationLabels($video);

              // Video Detection

              $duration = isset($result['Job']['Output']['Duration']) ? $result['Job']['Output']['Duration'] : null;
              $video->setDuration($duration);
              $video->setIsAwsJobProcessed('Y');
              $video->setStatus('active');
              $this->getVideoMapper()->saveVideo($video);
            }
          } catch (\Aws\Exception\AwsException $e) {
              echo $e->getMessage();
          }
        }

        echo "\n";
      }
    }
	}

  public function checkVideoJobs()
	{
    if($this->_debug){
      echo "\nGet Video Label Detection\n";
    }
    $filter = array(
      'label_detection_job_id_not_null_or_not_empty' => true,
      'is_aws_job_processed' => 'Y',
      'label_detection_status_null_or_empty' => true,
      'status' => 'active',
    );
    $order = array(
      'created_datetime',
    );
    $videos = $this->getVideoMapper()->fetchAll(false, $filter, $order);
    if(count($videos) > 0){
      foreach ($videos as $video){
        if($this->_debug){
          echo $video->getId() . "\n";
        }

        $serviceAmazonImageRekognition = $this->getServiceLocator()->get('ServiceAmazonImageRekognition');
        $serviceAmazonImageRekognition->getGetLabelDetection($video);
      }
    }

    if($this->_debug){
      echo "\nGet Video Content Moderation\n";
    }
    $filter = array(
      'content_moderation_job_id_not_null_or_not_empty' => true,
      'is_aws_job_processed' => 'Y',
      'content_moderation_status_null_or_empty' => true,
      'status' => 'active',
    );
    $order = array(
      'created_datetime',
    );
    $videos = $this->getVideoMapper()->fetchAll(false, $filter, $order);
    if(count($videos) > 0){
      foreach ($videos as $video){
        if($this->_debug){
          echo $video->getId() . "\n";
        }

        $serviceAmazonImageRekognition = $this->getServiceLocator()->get('ServiceAmazonImageRekognition');
        $serviceAmazonImageRekognition->getGetContentModeration($video);
      }
    }
  }
}
