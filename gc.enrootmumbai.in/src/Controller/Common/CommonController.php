<?php

namespace App\Controller\Common;

use App\Entity\Master\MstAreasInCity;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstState;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserInfo;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommonController
 * @package App\Controller\Common
 * @IsGranted("ROLE_APP_USER")
 */
class CommonController extends AbstractController
{
    /**
     * @Route("/core/location/state_list", name="location_state_list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function statesList(Request $request): Response
    {
        $country_id = $request->query->get('q');
        $stateList = $this->getDoctrine()->getRepository(MstState::class)->getStateListByCountryId($country_id);
        return $this->json($stateList);
    }

    /**
     * @Route("/core/location/city_list", name="location_city_list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function citiesList(Request $request): Response
    {
        $state_id = $request->query->get('q');
        $cityList = $this->getDoctrine()->getRepository(MstCity::class)->getCityListByStateId($state_id);
        return $this->json($cityList);
    }

    /**
     * @Route("/core/location/area_in_city_list", name="location_area_in_city_list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function areacitiesList(Request $request): Response
    {
        $city_id = $request->query->get('q');
        $areaInCityList = $this->getDoctrine()->getRepository(MstAreasInCity::class)->getAreaInCityListByCityId($city_id);
        return $this->json($areaInCityList);
    }

    /**
     * @Route("/core/company/member_organization_list", name="company_member_organization_list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function getMemberOrganizationList(Request $request): Response
    {
        $orgCompanyId = $request->query->get('q');
        $memberOrganizationList = $this->getDoctrine()->getRepository(AppUser::class)->getUserByCompanyId($orgCompanyId);
        $returnResultSet = array();
        foreach ($memberOrganizationList as $appUser) {
            $strOrgDetails = " Ind ";
            $objOrg =  $appUser->getTrnOrganizationDetails();
            if(!empty($objOrg) && !empty($objOrg[0])) {
                $strOrgDetails = " Org ". $objOrg[0]->getOrganizationName();
            }
            $returnResultSet[] = array( 'id'=> $appUser->getId(), 'name' => $appUser->getAppUserInfo()->getMstSalutation().' '
                .$appUser->getAppUserInfo()->getUserFirstName().' '.$appUser->getAppUserInfo()->getUserMiddleName().' '.$appUser->getAppUserInfo()->getUserLastName(). ' - '.$strOrgDetails) ;
        }
        return $this->json($returnResultSet);
    }
    /**
     * @Route("/core/company/member_organization_list_ce", name="company_member_organization_list_ce", methods={"GET",
     *     "POST"})
     * @param Request $request
     * @return Response
     */
    public function getMemberOrganizationWithCircleList(Request $request): Response
    {
        $orgCompanyId = $request->query->get('q');
        $arrCircleList = $this->getDoctrine()->getRepository(TrnCircle::class)->getAppUsersWithCircles($orgCompanyId);
        $memberOrganizationList = array();
        foreach ($arrCircleList as $circle){
            $memberOrganizationList[$circle->getAppUser()->getId()] = $circle->getAppUser();
        }

        $returnResultSet = array();
        foreach ($memberOrganizationList as $appUser) {
            $strOrgDetails = " Ind ";
            $objOrg =  $appUser->getTrnOrganizationDetails();
            if(!empty($objOrg) && !empty($objOrg[0])) {
                $strOrgDetails = " Org ". $objOrg[0]->getOrganizationName();
            }
            $returnResultSet[] = array( 'id'=> $appUser->getId(), 'name' => $appUser->getAppUserInfo()->getMstSalutation().' '
                .$appUser->getAppUserInfo()->getUserFirstName().' '.$appUser->getAppUserInfo()->getUserMiddleName().' '.$appUser->getAppUserInfo()->getUserLastName(). ' - '.$strOrgDetails) ;
        }
        return $this->json($returnResultSet);
    }

    /**
     * @Route("/core/company/get_bank_details", name="company_get_bank_details", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function getBankDetails(Request $request): Response
    {
        $arrResponseBankDetails = array("accountHolderName" => "", "accountNumber" => "", "ifscCode" => "", "mstBankAccountType" => "", "bankName" => "", "accountType" => "", "country_id" => "", "state_id" => "", "city_id" =>"", "country_name" => "", "state_name" => "", "city_name" =>"", "mstAreaOfInterest" => array() );

        $appUserId = $request->query->get('q');
        $objAppUser = $this->getDoctrine()->getRepository(AppUser::class)->find($appUserId);
        if(!empty($objAppUser)) {
            $arrTempData = array();
            $objOrg =  $objAppUser->getTrnOrganizationDetails();
            $objAppUserInfo = $objAppUser->getAppUserInfo();
            if(!empty($objAppUser) && !empty($objAppUser->getTrnBankDetails())) {
                $arrTrnBankDetails = $objAppUser->getTrnBankDetails();
                if(!empty($arrTrnBankDetails) && !empty($arrTrnBankDetails[0])) {
                    $objTrnBankDetails = $arrTrnBankDetails[0];
                    $arrResponseBankDetails = array("accountHolderName" => $objTrnBankDetails->getAccountHolderName(),
                        "accountNumber" => $objTrnBankDetails->getAccountNumber(),
                        "ifscCode" => $objTrnBankDetails->getIfscCode(), "mstBankAccountType" =>
                            $objTrnBankDetails->getMstBankAccountType()->getId(), "bankName" =>
                            $objTrnBankDetails->getBankName(), "accountType" =>
                            $objTrnBankDetails->getMstBankAccountType()->getBankAccountType(), "country_id" => "", "state_id" => "", "city_id" =>"", "country_name" => "", "state_name" => "", "city_name" =>"" );
                }
            }
            if (!empty($objAppUserInfo)) {
                if(!empty($objAppUserInfo->getMstCountry())){
                    $arrResponseBankDetails["country_id"] = $objAppUserInfo->getMstCountry()->getId();
                    $arrResponseBankDetails["country_name"] = $objAppUserInfo->getMstCountry()->getCountry();
                }
                if(!empty($objAppUserInfo->getMstState())){
                    $arrResponseBankDetails["state_id"] = $objAppUserInfo->getMstState()->getId();
                    $arrResponseBankDetails["state_name"] = $objAppUserInfo->getMstState()->getState();
                }

                if(!empty($objAppUserInfo->getMstCity())){
                    $arrResponseBankDetails["city_id"] = $objAppUserInfo->getMstCity()->getId();
                    $arrResponseBankDetails["city_name"] = $objAppUserInfo->getMstCity()->getCity();
                }
                $arrMstAreaOfInterest = $objAppUserInfo->getMstAreaInterest();

                foreach ($arrMstAreaOfInterest as $objMstAreaOfInterest) {
                    $arrTempData[] = array('id' => $objMstAreaOfInterest->getId(), 'name' =>
                        $objMstAreaOfInterest->getAreaInterest());
                }
            }
            $arrResponseBankDetails["mstAreaOfInterest"] = $arrTempData;
        }
        return $this->json($arrResponseBankDetails);
    }

    /**
     * @Route("/core/circle/event_list", name="circle_event_list", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function getCircleEventList(Request $request): Response
    {
        $circle_id = $request->query->get('q');
        $eventList = $this->getDoctrine()->getRepository(TrnCircleEvents::class)->getCircleEventList($circle_id);

        return $this->json($eventList);
    }

    /**
     * @Route("/core/pancard/check_unqiue", name="pancard_check_unqiue", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function checkPanCardNoUnique(Request $request): Response
    {
        $pancardNumber = $request->query->get('q');
        $arrData = $this->getDoctrine()->getRepository(AppUserInfo::class)->checkPanCardNoUnique($pancardNumber);
        if (empty($arrData))
            return $this->json(array("unique" => true));
        else
            return $this->json(array("unique" => false));
    }

    /**
     * @Route("/core/circle/get_area_of_interest", name="circle_area_of_interest", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function getCircleAreaOfInterest(Request $request): Response
    {
        $circle_id = $request->query->get('q');

        $objTrnCircle = $this->getDoctrine()->getRepository(TrnCircle::class)->find($circle_id);
        $arrTrnAreaOfInterests = $objTrnCircle->getTrnAreaOfInterests();
        $arrReturnData = array();
        foreach ($arrTrnAreaOfInterests as $TrnAreaOfInterest) {
            $arrData = array('primaryAreaOfInterest' => '', 'secondaryAreaOfInterest' => array());
            $arrData['primaryAreaOfInterest'] = $TrnAreaOfInterest->getAreaInterestPrimary()->getAreaInterest();
            $areaInterestSecondary = $TrnAreaOfInterest->getAreaInterestSecondary();
            foreach ($areaInterestSecondary as $areaInterest) {
                $arrData['secondaryAreaOfInterest'][] = $areaInterest->getAreaInterest();
            }
            $arrReturnData[] = $arrData;
        }
        return $this->json($arrReturnData);
    }

    /**
     * @Route("/core/circle/get_app_user_circles", name="circle_app_user_circles", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function getAppUserCircles(Request $request): Response
    {
        $appUserId = $request->query->get('q');
        $arrCircles = $this->getDoctrine()->getRepository(TrnCircle::class)->getAppUserCircles($appUserId);

        return $this->json($arrCircles);
    }

    /**
     * @Route("/core/user_email/check_unqiue", name="user_email_check_unqiue", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function checkEmailIdUnique(Request $request): Response
    {
        $emailId = trim($request->query->get('q'));
        $arrData = $this->getDoctrine()->getRepository(AppUserInfo::class)->findOneBy(array('userEmail' => $emailId));
        if (empty($arrData))
            return $this->json(array("unique" => true));
        else
            return $this->json(array("unique" => false));
    }


}
