<?php

namespace App\Service;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnAreaOfInterest;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnCrowdFundEventDistributedDetails;
use App\Entity\Transaction\TrnCrowdFundEventDocuments;
use App\Entity\Transaction\TrnCrowdFundEventOfflineTransfer;
use App\Entity\Transaction\TrnOrder;
use App\Entity\Transaction\TrnProductMedia;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class CrowdfundingEventService
{
    /*
     * @var EntityManagerInterface
     */
    private $em;

    private $fileUploaderHelper;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em, FileUploaderHelper $fileUploaderHelper) {
        $this->em = $em;
        $this->fileUploaderHelper = $fileUploaderHelper;
    }

    public function addEventDetails($trnCircleEvents, $primaryAreaOfInterest, $appUser, $arrSecondaryAI, $companyId) {

        $objMstJoinBy = $this->em->getRepository(MstJoinBy::class)->findOneBy(["joinBy" =>  'Closed']);
        $entityManager = $this->em;

        $objPrimaryAreaInterest = $this->em->getRepository(MstAreaInterest::class)->find
        ($primaryAreaOfInterest);
        $objTrnAreaOfInterest = new TrnAreaOfInterest();
        $objTrnAreaOfInterest->setAreaInterestPrimary($objPrimaryAreaInterest);
        $objSecondaryAI = null;
        $MstEventProductTypeObj = $this->em->getRepository(MstEventProductType::class)->findOneBy(["isActive" => true,
            'eventProductType' => 'Crowdfunding']);
        $trnCircleEvents->addMstEventProductType($MstEventProductTypeObj);
        $trnCircleEvents->setMstJoinBy($objMstJoinBy);
        $trnCircleEvents->setIsCrowdFunding(1);
        $trnCircleEvents->setMstCity($trnCircleEvents->getTrnCircle()->getMstCity());
        $trnCircleEvents->setMstState($trnCircleEvents->getTrnCircle()->getMstState());
        $trnCircleEvents->setMstCountry($trnCircleEvents->getTrnCircle()->getMstCountry());
        $trnCircleEvents->setMstStatus($this->em->getRepository(MstStatus::class)->findOneBy(["status"
        =>  'Pending Activation']));
        $trnCircleEvents->setCreatedOn(new \DateTime());
        $trnCircleEvents->setAppUser($appUser);
        $trnCircleEvents->setUserIpAddress($_SERVER['SERVER_ADDR']);
        $trnCircleEvents->setOrgCompany($this->em->getRepository(OrgCompany::class)->find($companyId) );
        $trnCircleEvents->setIsActive(0);
        $entityManager->persist($trnCircleEvents);
        $entityManager->flush();

        foreach ($arrSecondaryAI as  $key => $nSecAI) {
            $objSecondaryAI = $this->em->getRepository(MstAreaInterest::class)->find($nSecAI);
            $objTrnAreaOfInterest->addAreaInterestSecondary($objSecondaryAI);
        }
        $trnCircleEvents->addTrnAreaOfInterest($objTrnAreaOfInterest);
        $entityManager->persist($trnCircleEvents);
        $entityManager->flush();
    }

    public function addCrowdfundEventDetails($trnCircleEvents, $session) {

        $entityManager = $this->em;

        /*
         * Remove any Previous Data If Any
         * */
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0])){
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];

            $arrTrnCrowdFundEventDistributedDetails = $trnCrowdFundEvents->getTrnCrowdFundEventDistributedDetails();
            foreach ($arrTrnCrowdFundEventDistributedDetails as $trnCrowdFundEventDistributedDetails){
                $trnCrowdFundEvents->removeTrnCrowdFundEventDistributedDetail($trnCrowdFundEventDistributedDetails);
            }

            $entityManager->persist($trnCrowdFundEvents);
            $entityManager->flush();
            $crowdFundRaiserSubEvents = $session->get('crowdFundRaiserSubEvents', array());
            foreach ($crowdFundRaiserSubEvents as $crowdFundRaiserSubEvent) {
                $objTrnCrowdFundEventDistributedDetails = new TrnCrowdFundEventDistributedDetails();
                $objTrnCrowdFundEventDistributedDetails->setCampaignerName($crowdFundRaiserSubEvent['memberName']);
                $objTrnCrowdFundEventDistributedDetails->setMobileNumber($crowdFundRaiserSubEvent['memberMobileNumber']);
                $objTrnCrowdFundEventDistributedDetails->setCampaingerEmail($crowdFundRaiserSubEvent['memberEmailId']);
                $objTrnCrowdFundEventDistributedDetails->setTargetAmount($crowdFundRaiserSubEvent['distributeAmount']);
                $objTrnCrowdFundEventDistributedDetails->setTrnCrowdFundEvent($trnCrowdFundEvents);
                $entityManager->persist($objTrnCrowdFundEventDistributedDetails);
                $trnCrowdFundEvents->addTrnCrowdFundEventDistributedDetail($objTrnCrowdFundEventDistributedDetails);
            }
            $entityManager->persist($trnCrowdFundEvents);
            $entityManager->flush();
        }
    }

    public function addCrowdfundEventMedia($trnCircleEvents, $files, $youTubeArr, $primaryAreaOfInterest,
                                           $arrSecondaryAI, $documentDescription, $companyId, $publicFileFolder,
                                           $reportingArr, $uploadedDocuments) {

        $entityManager = $this->em;
        $objPrimaryAreaInterest = $this->em->getRepository(MstAreaInterest::class)->find
        ($primaryAreaOfInterest);

        foreach ($arrSecondaryAI as  $key => $nSecAI) {
            $objSecondaryAI = $this->em->getRepository(MstAreaInterest::class)->find($nSecAI);
        }

        $objProfileImageData = array();
        $arrImageGallery = array();
        if(isset($reportingArr['image']) && $reportingArr['image'] == 'on') {
            $objProfileImageData = $files->get('profileImage');
            $arrImageGallery = $files->get('imageGallery');
        } else {
            // remove old profile image if any
            $oldProfileImg = str_replace('files/', '', $trnCircleEvents->getProfileImage());
            if(!empty($oldProfileImg)) {
                $this->fileUploaderHelper->removeFile($oldProfileImg);
            }
            $trnCircleEvents->setProfileImage('');
        }

        // remove old documents
        $trnCrowdFundEventsArray = $trnCircleEvents->getTrnCrowdFundEvents();
        if(!empty($trnCrowdFundEventsArray) && !empty($trnCrowdFundEventsArray[0]) ) {
            $trnCrowdFundEvents = $trnCrowdFundEventsArray[0];
            $arrTrnCrowdFundEventDocuments = $trnCrowdFundEvents->getTrnCrowdFundEventDocuments();
            foreach ($arrTrnCrowdFundEventDocuments as $trnCrowdFundEventDocuments) {

                if(!in_array($trnCrowdFundEventDocuments->getUploadedFilePath(), $uploadedDocuments)){

                    // remove old profile image if any
                    $oldDocument = str_replace('files/', '', $trnCrowdFundEventDocuments->getUploadedFilePath());
                    if(!empty($oldDocument)) {
                        $this->fileUploaderHelper->removeFile($oldDocument);
                    }

                    $trnCrowdFundEvents->removeTrnCrowdFundEventDocument($trnCrowdFundEventDocuments);
                }
            }
        }

        if (!empty($files)) {

            $arrFilename = $files->get('filename');

            if (!empty($objProfileImageData) && !empty($objProfileImageData[0])) {
                $objProfileImageData = $objProfileImageData[0];
                $newFilename = $this->fileUploaderHelper->uploadPublicFile($objProfileImageData,
                    $trnCircleEvents->getName().
                    ' profileImage'.Uuid::uuid4()->toString(), null);
                $trnCircleEvents->setProfileImage($newFilename);
            }

            $entityManager->persist($trnCircleEvents);
            if (!empty($arrImageGallery)) {
                foreach ($arrImageGallery as $key => $objImageGallery) {
                    if (!empty($objImageGallery) && !empty($objImageGallery[0])) {
                        $objImageGallery = $objImageGallery[0];
                        $trnProductMedia = new TrnProductMedia();
                        $trnProductMedia->setMstAreaInterestPrimary($objPrimaryAreaInterest);
                        $trnProductMedia->setMstAreaInterestSecondary($objSecondaryAI);
                        $trnProductMedia->setOrgCompany($this->em->getRepository(OrgCompany::class)->find($companyId));
                        $trnProductMedia->setTrnCircle($trnCircleEvents->getTrnCircle());
                        $trnProductMedia->setTrnCircleEvents($trnCircleEvents);
                        $trnProductMedia->setMediaType('image');
                        $trnProductMedia->setMediaName('GalleryImage'.$trnCircleEvents->getId().$key);
                        $trnProductMedia->setMediaAltText('GalleryImage'.$trnCircleEvents->getId().$key);
                        $trnProductMedia->setMediaTitle('GalleryImage'.$trnCircleEvents->getId().$key);
                        $trnProductMedia->setIsActive(1);
                        $newFilename = $this->fileUploaderHelper->uploadPublicFile($objImageGallery, 'GalleryImage'
                            .$trnCircleEvents->getId().$key.Uuid::uuid4()->toString(), null);
                        $trnProductMedia->setMediaFileName($newFilename);
                        $trnProductMedia->setUploadedFilePath($publicFileFolder);
                        $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                        $trnProductMedia->setCreatedOn(new \DateTime());
                        $entityManager->persist($trnProductMedia);
                        $trnCircleEvents->addTrnProductMedium($trnProductMedia);
                    }
                }
                $entityManager->persist($trnCircleEvents);
            }


            if(!empty($arrFilename)) {

                foreach ($arrFilename as $key => $objFilename) {
                    $strDocDesc = "";
                    if (!empty($documentDescription) && !empty($documentDescription[$key])){
                        $strDocDesc = $documentDescription[$key];
                    }
                    if (!empty($objFilename)) {
                        $newFilename = $this->fileUploaderHelper->uploadPublicFile($objFilename, 'EventDocumentImage'
                            .$trnCircleEvents->getId().$key.Uuid::uuid4()->toString(), null);
                        $objTrnCrowdFundEventDocuments = new TrnCrowdFundEventDocuments();
                        $objTrnCrowdFundEventDocuments->setUploadedFilePath($newFilename);
                        $objTrnCrowdFundEventDocuments->setDocumentCaption($strDocDesc);
                        $objTrnCrowdFundEventDocuments->setTrnCrowdFundEvent($trnCrowdFundEvents);
                        $objTrnCrowdFundEventDocuments->setIsActive(1);
                        $entityManager->persist($objTrnCrowdFundEventDocuments);
                        $trnCrowdFundEvents->addTrnCrowdFundEventDocument($objTrnCrowdFundEventDocuments);
                    }
                }
                $entityManager->persist($trnCrowdFundEvents);
            }
        }

        if (!empty($youTubeArr)) {
            foreach ($youTubeArr as $key => $youtubeLink) {
                if (empty($youtubeLink))
                    continue;
                $trnProductMedia = new TrnProductMedia();
                $trnProductMedia->setMstAreaInterestPrimary($objPrimaryAreaInterest);
                $trnProductMedia->setMstAreaInterestSecondary($objSecondaryAI);
                $trnProductMedia->setOrgCompany($this->em->getRepository(OrgCompany::class)->find($companyId));
                $trnProductMedia->setTrnCircle($trnCircleEvents->getTrnCircle());
                $trnProductMedia->setTrnCircleEvents($trnCircleEvents);
                $trnProductMedia->setIsActive(1);
                $trnProductMedia->setMediaType('video');
                $trnProductMedia->setMediaName('YoutubeLink'.$trnCircleEvents->getId().$key);
                $trnProductMedia->setMediaURL($youtubeLink);
                $trnProductMedia->setUploadUserIpAddress($_SERVER['SERVER_ADDR']);
                $trnProductMedia->setCreatedOn(new \DateTime());
                $entityManager->persist($trnProductMedia);
                $trnCircleEvents->addTrnProductMedium($trnProductMedia);
            }
            $entityManager->persist($trnCircleEvents);
        }
        $entityManager->flush();
    }

}