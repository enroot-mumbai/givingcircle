<?php

namespace App\Controller\Portal;

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

class CommonController extends AbstractController
{

    /**
     * @Route("/portal/user_email/check_unqiue", name="portal_user_email_check_unqiue", methods={"GET","POST"})
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
    /**
     * @Route("/portal/location/state_list", name="portal_location_state_list", methods={"GET","POST"})
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
     * @Route("/portal/location/city_list", name="portal_location_city_list", methods={"GET","POST"})
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
     * @Route("/portal/location/area_in_city_list", name="portal_location_area_in_city_list", methods={"GET","POST"})
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
     * @Route("/portal/user_mob/check_unique", name="portal_user_mob_check_unqiue", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function checkUniqueMobileNo(Request $request) : Response
    {
        $mobileNumber = trim($request->query->get('mobileNo'));
        $mobCountryCode = trim($request->query->get('countryCode'));
        $arrData = $this->getDoctrine()->getRepository(AppUserInfo::class)->findOneBy(array('userMobileNumber' =>
            $mobileNumber, 'mobileCountryCode' => $mobCountryCode));
        if (empty($arrData))
            return $this->json(array("unique" => true));
        else
            return $this->json(array("unique" => false));
    }

}