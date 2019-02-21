<?php

namespace Aws\Service;

use Zend\Mvc\Controller\AbstractActionController;

class ServiceAmazonTranscribe extends AbstractActionController
{
  protected $_debug = true;

  public function getVideoMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('VideoMapper');
  }

	public function startTranscriptionJob($video)
	{
    $config = $this->getServiceLocator()->get('Config');
    try {
      $transcribe = new \Aws\TranscribeService\TranscribeServiceClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $result = $transcribe->startTranscriptionJob([
        'LanguageCode' => 'en-US', // REQUIRED
        'Media' => [ // REQUIRED
          'MediaFileUri' => "https://s3.amazonaws.com/" . $config['amazonApi']['Bucket Video Input']['bucket'] . "/" . $video->getId() . "/video.mp4",
        ],
        'MediaFormat' => 'mp4',
        // 'MediaSampleRateHertz' => <integer>,
        // 'OutputBucketName' => $config['amazonApi']['Bucket Transcribe Output']['bucket'],
        // 'Settings' => [
        //     'MaxSpeakerLabels' => <integer>,
        //     'ShowSpeakerLabels' => true || false,
        //     'VocabularyName' => '<string>',
        // ],
        'TranscriptionJobName' => $video->getId(), // REQUIRED
      ]);
      // print_r($result);

      if(isset($result['TranscriptionJob']['TranscriptionJobStatus'])){
        $video->setTranscriptionJobStatus($result['TranscriptionJob']['TranscriptionJobStatus']);
        $this->getVideoMapper()->saveVideo($video);
      }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getTranscriptionJob($video)
	{
    if($this->_debug){
      echo "\nVideo Transcribe Get Job";
    }

    $config = $this->getServiceLocator()->get('Config');
    try {
      $transcribe = new \Aws\TranscribeService\TranscribeServiceClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $result = $transcribe->getTranscriptionJob([
        'TranscriptionJobName' => $video->getId(), // REQUIRED
      ]);
      if(isset($result['TranscriptionJob']['TranscriptionJobStatus']) && $result['TranscriptionJob']['TranscriptionJobStatus']=='COMPLETED'){
        // echo $result['TranscriptionJob']['TranscriptionJobStatus'];
        if(isset($result['TranscriptionJob']['Transcript']['TranscriptFileUri'])){
          // echo $result['TranscriptionJob']['Transcript']['TranscriptFileUri'];
          $json = file_get_contents($result['TranscriptionJob']['Transcript']['TranscriptFileUri']);
          $video->setAwsTranscribeTranscript($json);
        }
        $video->setTranscriptionJobStatus($result['TranscriptionJob']['TranscriptionJobStatus']);
        $this->getVideoMapper()->saveVideo($video);
      }

      //print_r($result);
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  /*
  * https://www.yash.info/aws-srt-creator.htm
  */
  public function checkTranscribeJobs(){
    if($this->_debug){
      echo "\nTranscribe videos";
    }
    $config = $this->getServiceLocator()->get('Config');

    $filter = array(
      'transcription_job_status_not_null_or_not_empty' => true,
      'is_caption' => 'N',
    );
    $order = array(
      'created_datetime',
    );
    $videos = $this->getVideoMapper()->fetchAll(false, $filter, $order);
    if(count($videos) > 0){
      foreach ($videos as $video){
        if($this->_debug){
          // echo $video->getId() . "|" . $video->getTranscriptionJobStatus() . "\n";
        }

        if(is_null($video->getTranscriptionJobStatus())){
          $this->startTranscriptionJob($video);
          continue;
        }else if($video->getTranscriptionJobStatus()!='COMPLETED'){
          $this->getTranscriptionJob($video);
          continue;
        }else if($video->getAwsTranscribeTranscript()!=''){
          $response = json_decode($video->getAwsTranscribeTranscript());
          // print_r($response);
          if(count($response) > 0){
            $results = array();
            if(isset($response->results->items) && count($response->results->items) > 0){
              $webvtt = "WEBVTT\nX-TIMESTAMP-MAP=MPEGTS:0, LOCAL:00:00:00.000\n\n";
              $ctrPerLine = 0;
              foreach ($response->results->items as $item) {
                $startTime = isset($item->start_time) ? $item->start_time : null;
                $endTime = isset($item->end_time) ? $item->end_time : null;
                $alternatives = isset($item->alternatives) ? $item->alternatives : array();
                $content = null;
                $confidence  = null;
                if(count($alternatives) > 0){
                  $content = isset($alternatives[0]->content) ? $alternatives[0]->content : null;
                  $confidence = isset($alternatives[0]->confidence) ? $alternatives[0]->confidence : null;
                }

                $type = isset($item->type) ? $item->type : null;

                // echo "$startTime|$endTime|$content|$confidence|$type\n";
                $results[] = array(
                  'type' => $type,
                  'start_time' => $startTime,
                  'end_time' => $endTime,
                  'content' => $content,
                  'confidence' => $confidence,
                );
              } // items

              // print_r($results);
              if(count($results) > 0){
                $ctrKey = 0;
                foreach ($results as $key => $result) {
                  $isNewLine = false;
                  $startTime = null;
                  $endTime = null;
                  $ctrKey++;

                  if($result['type']=='pronunciation'){
                    $hours = floor($result['start_time'] / 3600);
                    $mins = floor($result['start_time'] / 60 % 60);
                    $secs = floor($result['start_time'] % 60);
                    $msecs = '000';
                    @list($seconds, $msecs) = explode('.', $result['start_time']);
                    $startTime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs) . "." . $msecs;

                    $endhours = floor($result['end_time'] / 3600);
                    $endmins = floor($result['end_time'] / 60 % 60);
                    $endsecs = floor($result['end_time'] % 60);
                    $endmsecs = '000';
                    @list($endseconds, $endmsecs) = explode('.', $result['end_time']);
                    $endTime = sprintf('%02d:%02d:%02d', $endhours, $endmins, $endsecs) . "." . $endmsecs;

                    $webvtt .= "$ctrKey\n";
                    $webvtt .= "$startTime --> $endTime\n";
                    $webvtt .= $result['content'] . "\n\n";
                  }
                }
                // echo $webvtt;

                $directory = $config['pathVideoUploads'];
                if(!file_exists($directory)){
                  mkdir($directory, 0755);
                }

                $directory = $directory . "/" . $video->getId();
                if(!file_exists($directory)){
                  mkdir($directory, 0755);
                }

                $vttFile = $directory . "/subtitle-en.vtt";
                if(file_exists($vttFile)){
                  unlink($vttFile);
                }
                $fp = fopen($vttFile, 'w');
                fwrite($fp, $webvtt);
                fclose($fp);

                if(file_exists($vttFile)){
                  $s3Client = new \Aws\S3\S3Client([
                   'version'     => 'latest',
                   'region'      => $config['amazonApi']['Region'],
                   'credentials' => [
                     'key'    => $config['amazonApi']['Access key ID'],
                     'secret' => $config['amazonApi']['Secret access key']
                   ],
                  ]);

                  try {
                     $result = $s3Client->putObject([
                       'Bucket'     => $config['amazonApi']['Bucket Video Output']['bucket'],
                       'Key'        => $video->getId() . "/" . "subtitle-en.vtt",
                       'SourceFile' => $vttFile,
                       // 'ACL' => 'public-read'
                     ]);
                     // print_r($result);

                     $video->setIsCaption('Y');
                     $this->getVideoMapper()->saveVideo($video);
                   } catch (\Aws\Exception\AwsException $e) {
                    echo $e->getMessage();
                   }
                }

              }
            } // items
          }

        } // !='COMPLETED'

      } // videos
    } // videos

  }
}
