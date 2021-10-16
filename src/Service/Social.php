<?php


namespace App\Service;


use App\Entity\Cms\CmsSocial;
use App\Entity\Master\MstSocial;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Social
{
    private $em;
    private $params;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->params = $params;
    }

    public function createPost($post, $image)
    {
        foreach ($post->getMstSocial() as $social)
        {
            if ($social->getSocialValue() == 'facebook')
            {
                $this->createFacebookPost($post, $image);
            }
        }
    }

    public function createFacebookPost($post, $image)
    {

        // Call the Facebook API
        $fb = new Facebook(
            [
                'app_id' => $this->params->get('fb_app_id'),           //Replace {your-app-id} with your app ID
                'app_secret' => $this->params->get('fb_app_secret'),   //Replace {your-app-secret} with your app secret
                'graph_api_version' => 'v5.0',
                'pseudo_random_string_generator' => 'openssl',
            ]);

        if ($image == null) {
            $linkData = [
                'link' => $post->getPostLink(),
                'caption' => $post->getPostCaption(),
                'description' => $post->getPostDescription(),
                'message' => $post->getPostMessage(),
                'picture' => $post->getPostPictureUrl(),
                'published' => '1'
            ];
            try {
                $response = $fb->post('/me/feed', $linkData, $this->params->get('fb_default_access_token'));

            } catch (FacebookResponseException $e) {
                // Returns Graph API errors when they occur
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (FacebookSDKException $e) {
                // Returns SDK errors when validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

        } else {

            $linkData = [
                'link' => $post->getPostLink(),
                'caption' => $post->getPostCaption(),
                'description' => $post->getPostDescription(),
                'message' => $post->getPostMessage(),
                'source' => $fb->fileToUpload($image->getPathName()),
                'published' => '1'
            ];
            try {
                $response = $fb->post('/me/photos', $linkData, $this->params->get('fb_default_access_token'));

            } catch (FacebookResponseException $e) {
                // Returns Graph API errors when they occur
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (FacebookSDKException $e) {
                // Returns SDK errors when validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

         }

        $me = $response->getGraphUser();
        if ($me['id']) {
            $cmsSocial = new CmsSocial();
            $cmsSocial->setCmsSocialPost($post);
            if (!empty($me['post_id'])) {
                $cmsSocial->setSocialSharePhotoId($me['id']);
                $cmsSocial->setSocialSharePostId($me['post_id']);
            } else {
                $cmsSocial->setSocialSharePostId($me['id']);
            }
            $cmsSocial->setMstSocial($this->em->getRepository(MstSocial::class)->find(1));
            $this->em->persist($cmsSocial);
            $this->em->flush();
        }

    }

    public function updateFacebookPost($post, $image)
    {
        // Call the Facebook API
        $fb = new Facebook(
            [
                'app_id' => $this->params->get('fb_app_id'),           //Replace {your-app-id} with your app ID
                'app_secret' => $this->params->get('fb_app_secret'),   //Replace {your-app-secret} with your app secret
                'graph_api_version' => 'v5.0',
                'pseudo_random_string_generator' => 'openssl',
            ]);

            if ($image == null) {
                $linkData = [
                    'link' => $post->getPostLink(),
                    'caption' => $post->getPostCaption(),
                    'description' => $post->getPostDescription(),
                    'message' => $post->getPostMessage(),
                    'picture' => $post->getPostPictureUrl(),
                    'published' => '1'
                ];

            } else {
                $linkData = [
                    'link' => $post->getPostLink(),
                    'caption' => $post->getPostCaption(),
                    'description' => $post->getPostDescription(),
                    'message' => $post->getPostMessage(),
                    'source' => $fb->fileToUpload($image->getPathName()),
                    'published' => '1'
                ];
            }

            try {
                $response = $fb->post($post->getCmsSocial()[0]->getSocialSharePostId(), $linkData, $this->params->get('fb_default_access_token'));

            } catch (FacebookResponseException $e) {
                // Returns Graph API errors when they occur
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (FacebookSDKException $e) {
                // Returns SDK errors when validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

        $me = $response->getGraphUser();
        return $me;
    }

    public function deleteFacebookPost($post)
    {
        // Call the Facebook API
        $fb = new Facebook(
            [
                'app_id' => $this->params->get('fb_app_id'),           //Replace {your-app-id} with your app ID
                'app_secret' => $this->params->get('fb_app_secret'),   //Replace {your-app-secret} with your app secret
                'graph_api_version' => 'v5.0',
                'pseudo_random_string_generator' => 'openssl',
            ]);

        try {

// Get your UserNode object, replace {access-token} with your token
            $response = $fb->delete($post->getCmsSocial()[0]->getSocialSharePostId(), array(), $this->params->get('fb_default_access_token'));

        } catch(FacebookResponseException $e) {
            // Returns Graph API errors when they occur
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(FacebookSDKException $e) {
            // Returns SDK errors when validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $me = $response->getGraphUser();
        return $me;

    }


}