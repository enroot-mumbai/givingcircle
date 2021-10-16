<?php


namespace App\Service;

use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnCircleEvents;
use App\Repository\Cms\CmsPressRoomRepository;
use Doctrine\ORM\EntityManagerInterface;


class NewsFilter
{
    private $em;
    /**
     * @var CmsPressRoomRepository
     */
    private $cmsPressRoomRepository;

    /**
     * NewsFilter constructor.
     * @param EntityManagerInterface $em
     * @param CmsPressRoomRepository $cmsPressRoomRepository
     */
    public function __construct(EntityManagerInterface $em, CmsPressRoomRepository $cmsPressRoomRepository)
    {
        $this->em                           = $em;
        $this->cmsPressRoomRepository       = $cmsPressRoomRepository;
    }

    public function filterNews($postData, $company_id)
    {
        $articles = '';

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

        $newsList = $this->cmsPressRoomRepository->getNewsBySearchParam($textSearch, $dateSearch, $company_id);

        return $newsList;

    }

    public function filterBlog($postData, $company_id)
    {
        $searchParam = $postData->request->get('searchparam');
        $articles = $this->cmsArticleRepository->getBlogBySearchParam($searchParam, $company_id);
        return $articles;

    }

    /**
     * @param MstStatus $objMstStatus
     * @param $postData
     * @param $company_id
     * @return mixed
     */
    public function filterProjects(MstStatus $objMstStatus, $postData, $company_id){
        $highLightId = $postData->request->get('highlightId');
        $interestId = $postData->request->get('interestId');
        $arrCircleList = $this->trnCircleRepository->getAllCirclesBasedOnFilter($company_id, $objMstStatus,
            $highLightId, $interestId);
        return $arrCircleList;
    }

    /**
     * @param MstStatus $objMstStatus
     * @param $postData
     * @param $company_id
     * @return mixed
     */
    public function filterProjectsEvents(MstStatus $objMstStatus, $postData, $company_id) {
        $nCircleId = $postData->get('circleId');
        $eventTime = $postData->get('eventTime');
        $searchText = $postData->get('searchText');
        $eventProductType = $postData->get('eventProductType');

        $expiredObjMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status" => 'Expired']);

        return $this->em->getRepository(TrnCircleEvents::class)->getAllEventOnFilter($nCircleId, $eventTime, $company_id,
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