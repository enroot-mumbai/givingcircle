<?php


namespace App\Service;

use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Cms\CmsArticleRepository;
use App\Repository\Transaction\TrnCircleRepository;
use App\Repository\Transaction\TrnCircleEventsRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChangeMakerFilter
{
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
     * ChangeMakerFilter constructor.
     * @param EntityManagerInterface $em
     * @param CmsArticleRepository $cmsArticleRepository
     * @param TrnCircleRepository $trnCircleRepository
     * @param TrnCircleEventsRepository $trnCircleEventsRepository
     */
    public function __construct(EntityManagerInterface $em, CmsArticleRepository $cmsArticleRepository, TrnCircleRepository $trnCircleRepository,
                                TrnCircleEventsRepository $trnCircleEventsRepository)
    {
        $this->em                         = $em;
        $this->cmsArticleRepository       = $cmsArticleRepository;
        $this->trnCircleRepository        = $trnCircleRepository;
        $this->trnCircleEventsRepository  = $trnCircleEventsRepository;
    }

    public function filterChangeMaker($postData, $company_id)
    {
        $articles = '';
        // Check if the filter is based on interest
        $interestId = $postData->request->get('interestId');
        $searchParam = $postData->request->get('searchparam');

        // Check if the filter is based on search
        if ($searchParam && $interestId != 'all' ) {
            $articles = $this->cmsArticleRepository->getChangeMakerBySearchParamAndInterest($searchParam, $interestId, $company_id);
        } elseif ($searchParam) {
            $articles = $this->cmsArticleRepository->getChangeMakerBySearchParam($searchParam, $company_id);
        } else if ($interestId) {
            if ($interestId == 'all') {
                $articles = $this->cmsArticleRepository->findBy(['mstArticleCategory' => 2, 'orgCompany' => $company_id, 'isActive' => 1], ['articleCreateDateTime' => 'DESC']);
            } else {
                $articles = $this->cmsArticleRepository->getArticleByInterestId($interestId, $company_id);
            }
        }

        return $articles;

    }

    public function filterBlog($postData, $company_id)
    {
        $searchParam = array();
        $textSearch = '';
        if($postData->request->get('searchText') && $postData->request->get('searchText') != '') {
            $textSearch = $postData->request->get('searchText');
        }

        if($postData->request->get('yearValue') && $postData->request->get('yearValue') != '') {
            $searchParam['year'] = $postData->request->get('yearValue');
        }

        if($postData->request->get('monthValue') && $postData->request->get('monthValue') != '') {
            $searchParam['month'] = $postData->request->get('monthValue');
        }

        $dateSearch = '';
        if(array_key_exists('year',$searchParam) && array_key_exists('month',$searchParam)) {
            $dateSearch = $searchParam['year'].'-'.$searchParam['month'].'-%';
        } else if(array_key_exists('year',$searchParam)) {
            $dateSearch = $searchParam['year'].'-%';
        } else if(array_key_exists('month',$searchParam)) {
            $dateSearch = '%-'.$searchParam['month'].'-%';
        }

        $articles = $this->cmsArticleRepository->getBlogBySearchParam($textSearch, $dateSearch, $company_id);
        return $articles;

    }

    /**
     * @param MstStatus $objMstStatus
     * @param $postData
     * @param $company_id
     * @return mixed
     */
    public function filterProjects(MstStatus $objMstStatus, $postData, $company_id){

        $highLightId = $postData->request->get('highlightId'); // type
        $interestId = $postData->request->get('interestId'); // category
        $searchText = $postData->request->get('searchText'); // searchText

        $arrCircleList = $this->trnCircleRepository->getAllCirclesBasedOnFilter($company_id, $objMstStatus,
            $highLightId, $interestId, $searchText);
        return $arrCircleList;
    }

    /**
     * @param MstStatus $objMstStatus
     * @param $changeMakerUserId
     * @param $company_id
     * @return mixed
     */
    public function filterUserProjects(MstStatus $objMstStatus, $changeMakerUserId, $company_id){

        $arrCircleList = $this->trnCircleRepository->getAppUserActiveCircles($changeMakerUserId, $objMstStatus);
        return $arrCircleList;
    }

    /**
     * @param MstStatus $objMstStatus
     * @param $postData
     * @param $company_id
     * @param MstStatus $expiredObjMstStatus
     * @return mixed
     */
    public function filterProjectsEvents(MstStatus $objMstStatus, $postData, $company_id) {
        $nCircleId = $postData->get('circleId');
        $eventTime = $postData->get('eventTime');
        $searchText = $postData->get('searchText');
        $eventProductType = $postData->get('eventProductType');

        $expiredObjMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);

        return $this->trnCircleEventsRepository->getAllEventOnFilter($nCircleId, $eventTime, $company_id,
            $eventProductType, $objMstStatus, $searchText, $expiredObjMstStatus);
    }

    public function filterVolunteerDiaries($postData, $company_id)
    {
        $volunteer_articles = '';
        $searchParam = $postData->request->get('searchparam');

        if ($searchParam) {
            // if searchParam set to some value
            $volunteer_articles = $this->cmsArticleRepository->getChangeMakerBySearchParam($searchParam, $company_id);
        } else {
            // search all volunteers to clear filter
            $volunteer_articles = $this->cmsArticleRepository->findBy(['mstArticleCategory' => 3,
                'orgCompany' => $company_id, 'isActive' => 1], ['sequenceNo' => 'ASC']);
        }
        return $volunteer_articles;
    }
}