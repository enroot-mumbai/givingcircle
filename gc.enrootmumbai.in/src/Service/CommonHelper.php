<?php

namespace App\Service;

use DateInterval;
use DateTime;

class CommonHelper
{
    /**
     * @param string $string
     * @return string
     */
    public function slugify(string $string): string
    {
        return preg_replace('/\s+/', '-', mb_strtolower(trim(strip_tags($string)), 'UTF-8'));
    }

    /**
     * @return false|string
     */
    public function tokenTime()
    {
        $tokenTime = date("yhmisd");
        return $tokenTime;
    }

    public function yearSelection($years)
    {
        $currentYear = date('Y');
        $yearRange = range($currentYear, $currentYear + $years);
        $yearArray = array();
        foreach ($yearRange as $key => $value)
        {
            $yearArray[$value] = $value;
        }
        return $yearArray;

    }

    // Periodic Values
    /**
     * @return string[]
     */
    public function periodic()
    {
        $periodic = array(
            'Monthly' => 'monthly',
            'Quarterly' => 'quarterly',
            'Half-Yearly' => 'half-yearly',
            'Yearly' => 'yearly'
        );
        return $periodic;
    }

    /**
     * @return string[]
     */
    public function numberType()
    {
        $number = array(
            'Percent' => 'percent',
            'Amount' => 'amount',
        );
        return $number;
    }

    /**
     * @return string[]
     */
    public function mediaType()
    {
        $mediaType = array(
            'Image' => 'image',
            'Video' => 'video',
        );
        return $mediaType;
    }

    /**
     * @return string[]
     */
    public function defaultBannerSize()
    {
        $bannerSize = array(
            'desktop' => ['width' => 1500, 'height' => 350],
            'tablet' => ['width' => 767, 'height' => 400],
            'mobile' => ['width' => 350, 'height' => 350],
        );
        return $bannerSize;
    }

    /**
     * @return string[]
     */
    public function imageType()
    {
        $mediaType = array(
            'Profile' => 'profile',
            'Background' => 'background',
            'Gallery' => 'gallery',
        );
        return $mediaType;
    }

    // Function to generate OTP
    /**
     * @param $n
     * @return string
     */
    public function generateNumericOTP($n) {

        // Take a generator string which consist of
        // all numeric digits
        $generator = "1357902468";

        // Iterate for n-times and pick a single character
        // from generator and append it to $result

        // Login for generating a random character from generator
        //     ---generate a random number
        //     ---take modulus of same with length of generator (say i)
        //     ---append the character at place (i) from generator to result

        $result = "";

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }

        // Return result
        return $result;
    }

    /**
     * @param $string
     * @return string|string[]|null
     */
    public function clean($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function mediaPosition(): array
    {
        return array(
            'Left to Text' => 'left',
            'Right to Text' => 'right'
        );
    }

    public function bannerPosition(): array
    {
        return array(
            'Top' => 'top',
            'Right' => 'right',
            'Left' => 'left',
            'Middle' => 'middle'
        );
    }

    /**
     * @return string[]
     */
    public function environmentType(): array
    {
        $environmentType = array(
            'Test' => 'test',
            'Prod' => 'prod',
        );
        return $environmentType;
    }

    /**
     * @return string[]
     */
    public function distanceData(): array
    {
        $distanceData = array(
            '10 KM' => '10 KM',
            '20 KM' => '20 KM',
            '30 KM' => '30 KM',
        );
        return $distanceData;
    }

    public function timeList()
    {
        $timeArray = range(0,24);
        $resultArray = array();
        foreach ($timeArray as $timeVal) {
            $resultVal = str_pad($timeVal,2,'0',STR_PAD_LEFT).':00';
            $timestamp = new \DateTime("2013-01-23 $timeVal:00");
//            $resultArray[$timestamp] = $resultVal;
            $resultArray[$resultVal] = $timestamp;
        }
        return $resultArray;
    }

    public function daysList() {
        return ['Sunday' => 'Sun', 'Monday' => 'Mon','Tuesday' => 'Tue','Wednesday' => 'Wed','Thursday' => 'Thu',
            'Friday' => 'Fri','Saturday' => 'Sat'];
    }
}
