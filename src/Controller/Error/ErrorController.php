<?php

namespace App\Controller\Error;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error", name="error_index")
     * @param $exception
     * @param $logger
     * @param Request $request
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index($exception,$logger,Request $request)
    {

        $data = explode("/",str_replace("'","",$request->getRequestUri()));
        if (empty($data[count($data)-1])){

            $data = $data[count($data)-2];
        }else{
            $data = $data[count($data)-1];
        }
        $errChangeMakerData[]='sonali-ghosh';
        $errChangeMakerData[]='monica-bisht';
        $errChangeMakerData[]='ravi-and-shital-narayanan';
        $errChangeMakerData[]='amit-basu';
        $errChangeMakerData[]='nirmal-jyot';
        $errChangeMakerData[]='sana-arora';
        $errChangeMakerData[]='manoj-lubana';
        $errChangeMakerData[]='sania-siddiqui';
        $errChangeMakerData[]='sampath-dangeti';
        $errChangeMakerData[]='sagar-vasoya';
        $errChangeMakerData[]='ravindra-salunke';
        $errChangeMakerData[]='divya-shlokam';
        $errChangeMakerData[]='neeraj-dahiya';
        $errChangeMakerData[]='atul-ajmera';
        $errChangeMakerData[]='shivika-and-madhur';
        if (in_array($data,$errChangeMakerData)){
            return $this->redirect("https://www.givingcircle.in/change-makers/$data",301);
        }
        $errBlogData[]="ways-to-motivate-employees-to-volunteer-for-indias-growth-story";
        $errBlogData[]='employee-volunteering-is-a-strategy-within-csr-and-not-csr-strategy';
        $errBlogData[]='how-to-look-for-the-right-charity-ngo-to-see-my-funds-are-put-to-right-use';
        $errBlogData[]='urban-india-to-the-rescue-of-urban-india';
        $errBlogData[]='importance-of-self-care-in-a-social-work-career';
        $errBlogData[]='how-food-is-driving-the-underprivileged-children-to-school';
        $errBlogData[]='people-are-quitting-high-paying-jobs-to-choose-community-service-as-a-career-heres-why';
        $errBlogData[]='how-to-take-care-of-street-dogs-this-mumbai-couple-might-just-give-you-the-inspiration';
        $errBlogData[]='research-proves-that-social-work-improves-adult-mental-health';
        $errBlogData[]='power-of-local-communities';
        $errBlogData[]='5-ways-to-support-the-society-during-the-covid-19';
        $errBlogData[]='house-helps-deserves-her-pay-article';
        $errBlogData[]='mahindra-and-mahindra-offers-to-convert-resorts-for-covid-2019-patients';
        $errBlogData[]='people-are-willingly-taking-a-salary-cut-to-support-covid-19-national-funds-in-india';
        $errBlogData[]='reliance-industries-helps-the-country-fight-the-covid-19-pandemic';
        $errBlogData[]='tata-pledges-to-pay-its-daily-wages-workers';
        $errBlogData[]='top-corporates-open-doors-for-daily-supplies';
        $errBlogData[]='the-government-of-indias-fight-against-coronavirus';
        $errBlogData[]='the-local-do-gooders-to-the-rescue';
        $errBlogData[]='9-things-you-can-do-to-help-others-without-spending-a-penny';

        if (in_array($data,$errBlogData)){
            return $this->redirect("https://www.givingcircle.in/blog/$data",301);
        }

        $errcode = $exception->getStatusCode();
        if ($errcode == 404){
            $errmsg = "Sorry, this page can't be found.";
        }else{
            $errmsg = $exception->getStatusText();
        }
        return $this->render('portal/error/index.html.twig', [
            'errorcode' => $errcode,
            'errormsg' => $errmsg,
        ]);
    }
}
