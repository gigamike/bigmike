<?php

namespace Aws\Service;

use Zend\Mvc\Controller\AbstractActionController;

use Video\Model\VideoDetectLabelEntity;
use Video\Model\VideoModerationLabelEntity;
use Video\Model\VideoImageFacialAnalysisEntity;
use Video\Model\VideoImageDetectTextEntity;

class ServiceAmazonImageRekognition extends AbstractActionController
{
  protected $_debug = true;

  public function getVideoMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('VideoMapper');
  }

  public function getVideoDetectLabelMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('VideoDetectLabelMapper');
  }

  public function getVideoModerationLabelMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('VideoModerationLabelMapper');
  }

  public function getVideoImageDetectTextMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('VideoImageDetectTextMapper');
  }

  public function getVideoImageFacialAnalysisMapper()
  {
    $sm = $this->getServiceLocator();
    return $sm->get('VideoImageFacialAnalysisMapper');
  }

	public function getDetectLabel($video){
    if($this->_debug){
      echo "\nVideo Image Detect Label";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      // Amazon Rekognition
	    $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

	    $image = array(
        'Image' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Output']['bucket'],
            'Name' => $video->getId() . "/" . $config['amazonApi']['Bucket Video Output']['thumbnail'],
          ],
        ],
	    );
	    $result = $rekognition->detectLabels($image);
	    // print_r($result);

	    $labels = isset($result['Labels']) ? $result['Labels'] : null;
	    if(is_array($labels) && count($labels) > 0){
        foreach ($labels as $label){
          $name = isset($label['Name']) ? $label['Name'] : null;
          $confidence = isset($label['Confidence']) ? $label['Confidence'] : null;

          if($confidence <= 80){
            continue;
          }

          if($name){
            $videoDetectLabelMapper = new VideoDetectLabelEntity();
            $videoDetectLabelMapper->setVideoId($video->getId());
            $videoDetectLabelMapper->setName($name);
            $videoDetectLabelMapper->setMediaType('image');
            $videoDetectLabelMapper->setConfidence($confidence);
            $this->getVideoDetectLabelMapper()->saveVideoDetectLabel($videoDetectLabelMapper);
          }
        }
	    }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getDetectText($video){
    if($this->_debug){
      echo "\nVideo Image Detect Text";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      // Amazon Rekognition
	    $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $image = array(
        'Image' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Output']['bucket'],
            'Name' => $video->getId() . "/" . $config['amazonApi']['Bucket Video Output']['thumbnail'],
          ],
        ],
	    );
	    $result = $rekognition->detectText($image);
	    // print_r($result);

      $textDetections = isset($result['TextDetections']) ? $result['TextDetections'] : null;
	    if(is_array($textDetections) && count($textDetections) > 0){
        foreach ($textDetections as $textDetection){
          $detectedText = isset($textDetection['DetectedText']) ? $textDetection['DetectedText'] : null;
          $type = isset($textDetection['Type']) ? $textDetection['Type'] : null;
          $confidence = isset($textDetection['Confidence']) ? $textDetection['Confidence'] : null;

          if($confidence <= 80){
            continue;
          }

          $videoImageDetectText = new VideoImageDetectTextEntity();
          $videoImageDetectText->setVideoId($video->getId());
          $videoImageDetectText->setDetectText($detectedText);
          $videoImageDetectText->setType($type);
          $videoImageDetectText->setConfidence($confidence);
          $this->getVideoImageDetectTextMapper()->saveVideoImageDetectText($videoImageDetectText);
        }
	    }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getDetectModerationLabels($video){
    if($this->_debug){
      echo "\nVideo Image Detect Moderation Labels";
    }

    $config = $this->getServiceLocator()->get('Config');
    try {
      // Amazon Rekognition
	    $rekognition = new \Aws\Rekognition\RekognitionClient([
	        'version'     => 'latest',
	        'region'      => $config['amazonApi']['Region'],
	        'credentials' => [
	            'key'    => $config['amazonApi']['Access key ID'],
	            'secret' => $config['amazonApi']['Secret access key']
	        ]
	    ]);

      $image = array(
        'Image' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Output']['bucket'],
            'Name' => $video->getId() . "/" . $config['amazonApi']['Bucket Video Output']['thumbnail'],
          ],
        ],
	    );
	    $result = $rekognition->detectModerationLabels($image);
	    // print_r($result);

      $labels = isset($result['ModerationLabels']) ? $result['ModerationLabels'] : null;
	    if(is_array($labels) && count($labels) > 0){
        foreach ($labels as $label){
          $name = isset($label['Name']) ? $label['Name'] : null;
          $parentName = isset($label['ParentName']) ? $label['ParentName'] : null;
          $confidence = isset($label['Confidence']) ? $label['Confidence'] : null;

          if($name){
            $videoModerationLabel = new VideoModerationLabelEntity();
            $videoModerationLabel->setVideoId($video->getId());
            $videoModerationLabel->setMediaType('image');
            $videoModerationLabel->setName($name);
            $videoModerationLabel->setParentName($parentName);
            $videoModerationLabel->setConfidence($confidence);
            $this->getVideoModerationLabelMapper()->saveVideoModerationLabel($videoModerationLabel);
          }
        }
	    }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getDetectFaces($video){
    if($this->_debug){
      echo "\nVideo Image Facial Analysis";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      // Amazon Rekognition
	    $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

	    $image = array(
        "Attributes" => ["ALL"],
        'Image' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Output']['bucket'],
            'Name' => $video->getId() . "/" . $config['amazonApi']['Bucket Video Output']['thumbnail'],
          ],
        ],
	    );
	    $result = $rekognition->detectFaces($image);
	    // print_r($result);

	    $faceDetails = isset($result['FaceDetails']) ? $result['FaceDetails'] : null;
	    if(count($faceDetails) > 0){
        foreach ($faceDetails as $faceDetail){
          $ageRangeHigh = isset($faceDetail['AgeRange']['High']) ? $faceDetail['AgeRange']['High'] : null;
          $ageRangeLow = isset($faceDetail['AgeRange']['Low']) ? $faceDetail['AgeRange']['Low'] : null;

          $beardConfidence = isset($faceDetail['Beard']['Confidence']) ? $faceDetail['Beard']['Confidence'] : null;

          $beardValue = isset($faceDetail['Beard']['Value']) ? $faceDetail['Beard']['Value'] : null;
          $beardValue = ($beardValue=='1') ? 'Y' : 'N';

          $confidence = isset($faceDetail['Confidence']) ? $faceDetail['Confidence'] : null;

          $emotionsHappyConfidence = null;
          $emotionsSadConfidence = null;
          $emotionsAngryConfidence = null;
          $emotionsConfusedConfidence = null;
          $emotionsDisgustedConfidence = null;
          $emotionsSuprisedConfidence = null;
          $emotionsCalmConfidence = null;
          $emotionsUknownConfidence = null;
          $emotions = isset($faceDetail['Emotions']) ? $faceDetail['Emotions'] : null;

          if(count($emotions) > 0){
            foreach ($emotions as $emotion){
              $confidence = isset($emotion['Confidence']) ? $emotion['Confidence'] : null;
              $type = isset($emotion['Type']) ? $emotion['Type'] : null;

              switch ($type){
                case 'HAPPY':
                  $emotionsHappyConfidence = $confidence;
                  break;
                case 'SAD':
                  $emotionsSadConfidence = $confidence;
                  break;
                case 'ANGRY':
                  $emotionsAngryConfidence = $confidence;
                  break;
                case 'CONFUSED':
                  $emotionsConfusedConfidence = $confidence;
                  break;
                case 'DISGUSTED':
                  $emotionsDisgustedConfidence = $confidence;
                  break;
                case 'SURPRISED':
                  $emotionsSuprisedConfidence = $confidence;
                  break;
                case 'CALM':
                  $emotionsCalmConfidence = $confidence;
                  break;
                case 'UNKNOWN':
                  $emotionsUknownConfidence = $confidence;
                  break;
                default:
              }
            }
          }
          $eyeglassesConfidence = isset($faceDetail['Eyeglasses']['Confidence']) ? $faceDetail['Eyeglasses']['Confidence'] : null;
          $eyeglassesValue = isset($faceDetail['Eyeglasses']['Value']) ? $faceDetail['Eyeglasses']['Value'] : null;
          $eyeglassesValue = ($eyeglassesValue=='1') ? 'Y' : 'N';

          $eyesOpenConfidence = isset($faceDetail['EyesOpen']['Confidence']) ? $faceDetail['EyesOpen']['Confidence'] : null;
          $eyesOpenValue = isset($faceDetail['EyesOpen']['Value']) ? $faceDetail['EyesOpen']['Value'] : null;
          $eyesOpenValue = ($eyesOpenValue=='1') ? 'Y' : 'N';

          $genderConfidence = isset($faceDetail['Gender']['Confidence']) ? $faceDetail['Gender']['Confidence'] : null;
          $genderValue = isset($faceDetail['Gender']['Value']) ? $faceDetail['Gender']['Value'] : 'Male';

          $mouthOpenConfidence = isset($faceDetail['MouthOpen']['Confidence']) ? $faceDetail['MouthOpen']['Confidence'] : null;
          $mouthOpenValue = isset($faceDetail['']['MouthOpen']['Value']) ? $faceDetail['MouthOpen']['Value'] : null;
          $mouthOpenValue = ($mouthOpenValue=='1') ? 'Y' : 'N';

          $mustacheConfidence = isset($faceDetail['Mustache']['Confidence']) ? $faceDetail['Mustache']['Confidence'] : null;
          $mustacheValue = isset($faceDetail['Mustache']['Value']) ? $faceDetail['Mustache']['Value'] : null;
          $mustacheValue = ($mustacheValue=='1') ? 'Y' : 'N';

          $smileConfidence = isset($faceDetail['Smile']['Confidence']) ? $faceDetail['Smile']['Confidence'] : null;
          $smileValue = isset($faceDetail['Smile']['Value']) ? $faceDetail['Smile']['Value'] : null;
          $smileValue = ($smileValue=='1') ? 'Y' : 'N';

          $sunglassesConfidence = isset($faceDetail['Sunglasses']['Confidence']) ? $faceDetail['Sunglasses']['Confidence'] : null;
          $sunglassesValue = isset($faceDetail['Sunglasses']['Value']) ? $faceDetail['Sunglasses']['Value'] : null;
          $sunglassesValue = ($sunglassesValue=='1') ? 'Y' : 'N';

          $videoImageFacialAnalysis = $this->getVideoImageFacialAnalysisMapper()->getVideoImageFacialAnalysisByVideoId($video->getId());
          if(!$videoImageFacialAnalysis){
            $videoImageFacialAnalysis = new VideoImageFacialAnalysisEntity();
            $videoImageFacialAnalysis->setVideoId($video->getId());
            $videoImageFacialAnalysis->setAgeRangeHigh($ageRangeHigh);
            $videoImageFacialAnalysis->setAgeRangeLow($ageRangeLow);
            $videoImageFacialAnalysis->setBeardConfidence($beardConfidence);
            $videoImageFacialAnalysis->setBeardValue($beardValue);
            $videoImageFacialAnalysis->setConfidence($confidence);
            $videoImageFacialAnalysis->setEmotionsHappyConfidence($emotionsHappyConfidence);
            $videoImageFacialAnalysis->setEmotionsSadConfidence($emotionsSadConfidence);
            $videoImageFacialAnalysis->setEmotionsAngryConfidence($emotionsAngryConfidence);
            $videoImageFacialAnalysis->setEmotionsConfusedConfidence($emotionsConfusedConfidence);
            $videoImageFacialAnalysis->setEmotionsDisgustedConfidence($emotionsDisgustedConfidence);
            $videoImageFacialAnalysis->setEmotionsSuprisedConfidence($emotionsSuprisedConfidence);
            $videoImageFacialAnalysis->setEmotionsCalmConfidence($emotionsCalmConfidence);
            $videoImageFacialAnalysis->setEmotionsUknownConfidence($emotionsUknownConfidence);
            $videoImageFacialAnalysis->setEyeglassesConfidence($eyeglassesConfidence);
            $videoImageFacialAnalysis->setEyeglassesValue($eyeglassesValue);
            $videoImageFacialAnalysis->setEyesOpenConfidence($eyesOpenConfidence);
            $videoImageFacialAnalysis->setEyesOpenValue($eyesOpenValue);
            $videoImageFacialAnalysis->setGenderConfidence($genderConfidence);
            $videoImageFacialAnalysis->setGenderValue($genderValue);
            $videoImageFacialAnalysis->setMouthOpenConfidence($mouthOpenConfidence);
            $videoImageFacialAnalysis->setMouthOpenValue($mouthOpenValue);
            $videoImageFacialAnalysis->setMustacheConfidence($mustacheConfidence);
            $videoImageFacialAnalysis->setMustacheValue($mustacheValue);
            $videoImageFacialAnalysis->setSmileConfidence($smileConfidence);
            $videoImageFacialAnalysis->setSmileValue($smileValue);
            $videoImageFacialAnalysis->setSunglassesConfidence($sunglassesConfidence);
            $videoImageFacialAnalysis->setSunglassesValue($sunglassesValue);
            $this->getVideoImageFacialAnalysisMapper()->saveVideoImageFacialAnalysis($videoImageFacialAnalysis);
          }
        }
	    }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
	}

  public function getStartCelebrityRecognition($video){
    if($this->_debug){
      echo "\nVideo Start Celebrity Recognition";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getGetCelebrityRecognition($video){
    if($this->_debug){
      echo "Video Get Celebrity Recognition\n";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getStartContentModeration($video){
    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $media = array(
        'Video' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Input']['bucket'],
            'Name' => $video->getId() . "/video.mp4",
          ],
        ],
     );
     $result = $rekognition->StartContentModeration($media);
     // print_r($result);

      if(isset($result['JobId']) && !empty($result['JobId'])){
        $video->setContentModerationJobId($result['JobId']);
        $this->getVideoMapper()->saveVideo($video);
      }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getGetContentModeration($video){
    if($this->_debug){
      echo "\nVideo Get Content Moderation";
    }

    if($video->getContentModerationJobId() && $video->getContentModerationStatus()!='Y'){
      $config = $this->getServiceLocator()->get('Config');

      try {
        $rekognition = new \Aws\Rekognition\RekognitionClient([
          'version'     => 'latest',
          'region'      => $config['amazonApi']['Region'],
          'credentials' => [
            'key'    => $config['amazonApi']['Access key ID'],
            'secret' => $config['amazonApi']['Secret access key']
          ]
  	    ]);

        $result = $rekognition->getContentModeration([
          'JobId' => $video->getContentModerationJobId(), // REQUIRED
        ]);
        // print_r($result);

        $labels = isset($result['ModerationLabels']) ? $result['ModerationLabels'] : null;
  	    if(is_array($labels) && count($labels) > 0){
          foreach ($labels as $label){
            $name = isset($label['Name']) ? $label['Name'] : null;
            $parentName = isset($label['ParentName']) ? $label['ParentName'] : null;
            $confidence = isset($label['Confidence']) ? $label['Confidence'] : null;

            if($confidence <= 80){
              continue;
            }

            if($name){
              $videoModerationLabel = new VideoModerationLabelEntity();
              $videoModerationLabel->setVideoId($video->getId());
              $videoModerationLabel->setMediaType('video');
              $videoModerationLabel->setName($name);
              $videoModerationLabel->setParentName($parentName);
              $videoModerationLabel->setConfidence($confidence);
              $this->getVideoModerationLabelMapper()->saveVideoModerationLabel($videoModerationLabel);
            }
          }
  	    }

        $video->setContentModerationStatus('Y');
        $this->getVideoMapper()->saveVideo($video);

      } catch (\Aws\Exception\AwsException $e) {
        echo $e->getMessage();
      }
    }
  }

  public function getStartFaceSearch($video){
    if($this->_debug){
      echo "Video Start Face Search\n";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $media = array(
        'CollectionId' => '', // REQUIRED
        'Video' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Input']['bucket'],
            'Name' => $video->getId() . "/video.mp4",
          ],
        ],
     );
     $result = $rekognition->startFaceSearch($media);
     // print_r($result);

      if(isset($result['JobId']) && !empty($result['JobId'])){
        $video->setFaceSearchJobId($result['JobId']);
        $this->getVideoMapper()->saveVideo($video);
      }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getGetFaceSearch($video){
    if($this->_debug){
      echo "Video Get Face Search\n";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  /*
  * Find Faces in video
  */
  public function getStartFaceDetection($video){
    if($this->_debug){
      echo "Video Start Face Detection\n";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $media = array(
        'Video' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Input']['bucket'],
            'Name' => $video->getId() . "/video.mp4",
          ],
        ],
     );
     $result = $rekognition->startFaceDetection($media);
     // print_r($result);

      if(isset($result['JobId']) && !empty($result['JobId'])){
        $video->setFaceDetectionJobId($result['JobId']);
        $this->getVideoMapper()->saveVideo($video);
      }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  /*
  * Get Faces on Video
  * not needed at this time
  */
  public function getGetFaceDetection($video){
    if($this->_debug){
      echo "Video Get Face Detection\n";
    }

    if($video->getFaceDetectionJobId() && $video->getFaceDetectionStatus()!='Y'){
      $config = $this->getServiceLocator()->get('Config');

      try {
        $rekognition = new \Aws\Rekognition\RekognitionClient([
          'version'     => 'latest',
          'region'      => $config['amazonApi']['Region'],
          'credentials' => [
            'key'    => $config['amazonApi']['Access key ID'],
            'secret' => $config['amazonApi']['Secret access key']
          ]
  	    ]);

        $result = $rekognition->getFaceDetection([
          'JobId' => $video->getFaceDetectionJobId(), // REQUIRED
        ]);
        // print_r($result);

        $faces = isset($result['Faces']) ? $result['Faces'] : null;
  	    if(count($faces) > 0){
          foreach ($faces as $face){
            $timestamp = isset($face['Timestamp']) ? $face['Timestamp'] : null;
          }
  	    }

        $video->setFaceDetectionStatus('Y');
        $this->getVideoMapper()->saveVideo($video);

      } catch (\Aws\Exception\AwsException $e) {
        echo $e->getMessage();
      }
    }
  }

  public function getStartLabelDetection($video){
    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $media = array(
        'Video' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Input']['bucket'],
            'Name' => $video->getId() . "/video.mp4",
          ],
        ],
     );
     $result = $rekognition->startLabelDetection($media);
     // print_r($result);

     if(isset($result['JobId']) && !empty($result['JobId'])){
       $video->setLabelDetectionJobId($result['JobId']);
       $this->getVideoMapper()->saveVideo($video);
     }

    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  public function getGetLabelDetection($video){
    if($this->_debug){
      echo "\nVideo Get Label Detection";
    }

    if($video->getLabelDetectionJobId() && $video->getLabelDetectionStatus()!='Y'){
      $config = $this->getServiceLocator()->get('Config');

      try {
        $rekognition = new \Aws\Rekognition\RekognitionClient([
          'version'     => 'latest',
          'region'      => $config['amazonApi']['Region'],
          'credentials' => [
            'key'    => $config['amazonApi']['Access key ID'],
            'secret' => $config['amazonApi']['Secret access key']
          ]
        ]);

        $result = $rekognition->getLabelDetection([
          'JobId' => $video->getLabelDetectionJobId(), // REQUIRED
        ]);
        // print_r($result);

        $labels = isset($result['Labels']) ? $result['Labels'] : null;
        if(is_array($labels) && count($labels) > 0){
          foreach ($labels as $label){
            $timestamp = isset($label['Timestamp']) ? $label['Timestamp'] : null;
            $name = isset($label['Label']['Name']) ? $label['Label']['Name'] : null;
            $confidence = isset($label['Label']['Confidence']) ? $label['Label']['Confidence'] : null;

            // echo "video|$timestamp|$name|$confidence\n";

            if($confidence <= 80){
              continue;
            }

            if($name){
              $videoDetectLabelMapper = new VideoDetectLabelEntity();
              $videoDetectLabelMapper->setVideoId($video->getId());
              $videoDetectLabelMapper->setName($name);
              $videoDetectLabelMapper->setMediaType('video');
              $videoDetectLabelMapper->setConfidence($confidence);
              $videoDetectLabelMapper->setVideoTimestamp($timestamp);
              $this->getVideoDetectLabelMapper()->saveVideoDetectLabel($videoDetectLabelMapper);
            }
          }
        }

        $video->setLabelDetectionStatus('Y');
        $this->getVideoMapper()->saveVideo($video);

      } catch (\Aws\Exception\AwsException $e) {
        echo $e->getMessage();
      }
    }
  }

  /*
  * Find Persons in video
  */
  public function getStartPersonTracking($video){
    if($this->_debug){
      echo "Video Start Person Tracking\n";
    }

    $config = $this->getServiceLocator()->get('Config');

    try {
      $rekognition = new \Aws\Rekognition\RekognitionClient([
        'version'     => 'latest',
        'region'      => $config['amazonApi']['Region'],
        'credentials' => [
          'key'    => $config['amazonApi']['Access key ID'],
          'secret' => $config['amazonApi']['Secret access key']
        ]
	    ]);

      $media = array(
        'Video' => [ // REQUIRED
          'S3Object' => [
            'Bucket' => $config['amazonApi']['Bucket Video Input']['bucket'],
            'Name' => $video->getId() . "/video.mp4",
          ],
        ],
     );
     $result = $rekognition->startPersonTracking($media);
     // print_r($result);

     if(isset($result['JobId']) && !empty($result['JobId'])){
       $video->setPersonTrackingJobId($result['JobId']);
       $this->getVideoMapper()->saveVideo($video);
     }
    } catch (\Aws\Exception\AwsException $e) {
      echo $e->getMessage();
    }
  }

  /*
  * Find Persons in video
  */
  public function getGetPersonTracking($video){
    if($this->_debug){
      echo "Video Get Person Tracking\n";
    }

    if($video->getPersonTrackingJobId() && $video->getPersonTrackingStatus()!='Y'){
      $config = $this->getServiceLocator()->get('Config');

      try {
        $rekognition = new \Aws\Rekognition\RekognitionClient([
          'version'     => 'latest',
          'region'      => $config['amazonApi']['Region'],
          'credentials' => [
            'key'    => $config['amazonApi']['Access key ID'],
            'secret' => $config['amazonApi']['Secret access key']
          ]
  	    ]);

        $result = $rekognition->getPersonTracking([
          'JobId' => $video->getPersonTrackingJobId(), // REQUIRED
        ]);
        // print_r($result);

      } catch (\Aws\Exception\AwsException $e) {
        echo $e->getMessage();
      }
    }

  }
}
