<?php


namespace App\Service;

use App\Entity\Master\MstArticleCategory;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Cms\CmsArticleRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use App\Repository\Transaction\TrnCircleRepository;
use Doctrine\ORM\EntityManagerInterface;


class SearchHelper
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CmsArticleRepository
     */
    private $cmsArticleRepository;

    /**
     * @var TrnCircleRepository
     */
    private $trnCircleRepository;

    /**
     * @var TrnCircleEventsRepository
     */
    private $trnCircleEventsRepository;

    /**
     * SearchHelper constructor.
     * @param EntityManagerInterface $em
     * @param CmsArticleRepository $cmsArticleRepository
     * @param TrnCircleRepository $trnCircleRepository
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     */
    public function __construct(EntityManagerInterface $em, CmsArticleRepository $cmsArticleRepository,
                                TrnCircleRepository $trnCircleRepository, TrnCircleEventsRepository $trnCircleEventsRepository)
    {
        $this->em                           = $em;
        $this->cmsArticleRepository         = $cmsArticleRepository;
        $this->trnCircleRepository          = $trnCircleRepository;
        $this->trnCircleEventsRepository    = $trnCircleEventsRepository;
    }

    public function changeMakerListByName($searchText, $company_id)
    {
        $changeMakers = array();

        if ($searchText != '') {
            // if searchParam set to some value
            $mstCategory = $this->em->getRepository(MstArticleCategory::class)->findOneBy(['articleCategory' => 'Change Makers']);
            $changeMakers = $this->cmsArticleRepository->getChangeMakerByName($searchText, $company_id, $mstCategory);
        }
        return $changeMakers;
    }

    public function projectListByName($searchText, $company_id) {
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);

        $circleList = array();
        $circleList = $this->trnCircleRepository->getCircleByName($company_id, $objMstStatus, $searchText);

        return $circleList;
    }

    public function eventListByName($searchText, $company_id) {
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Activated']);
        $expiredObjMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);
        $arrMstEventProductTypeObj = $this->em->getRepository(MstEventProductType::class)->findBy(["isActive" => true]);
        $arrMstEventProductType = array();
        $eventList = null;

        foreach ($arrMstEventProductTypeObj as $MstEventProductType) {
            if ($MstEventProductType->getEventProductType() == 'Crowdfunding')
                continue;
            $arrMstEventProductType[] = $MstEventProductType->getId();
        }

        $arrInputParam['searchText'] = $searchText;
        $eventList = $this->trnCircleEventsRepository->getFundRaiserAndCrowdFundingEvents($objMstStatus, $company_id, $arrMstEventProductType, $arrInputParam, $expiredObjMstStatus);

        return $eventList;
    }

    public function volunteerListByName($searchText, $company_id)
    {
        $volunteers = array();

        if ($searchText != '') {
            // if searchParam set to some value
            $mstCategory = $this->em->getRepository(MstArticleCategory::class)->findOneBy(['articleCategory' => 'Volunteers']);
            $volunteers = $this->cmsArticleRepository->getChangeMakerByName($searchText, $company_id, $mstCategory);
        }
        return $volunteers;
    }
}