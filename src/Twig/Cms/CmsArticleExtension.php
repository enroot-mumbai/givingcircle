<?php


namespace App\Twig\Cms;

use App\Entity\Cms\CmsArticleComment;
use App\Entity\Cms\CmsArticle;
use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnCircle;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\NullLogger;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CmsArticleExtension  extends AbstractExtension
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_cms_article_count', [$this, 'getCmsArticleCount']),
            new TwigFunction('get_cms_articles', [$this, 'getCmsArticle']),
            new TwigFunction('get_cms_articles_limit', [$this, 'getCmsArticleLimit']),
            new TwigFunction('get_cms_article_comments', [$this, 'getCmsArticleComment']),
            new TwigFunction('get_cms_article_comments_count', [$this, 'getCmsArticleCommentCount']),
            new TwigFunction('get_content_by_slugname', [$this, 'getContentBySlugName']),
            new TwigFunction('get_other_cms_article_by_id', [$this, 'getOtherArticlesById']),
            new TwigFunction('get_change_maker_project', [$this, 'getChangeMakerProject']),
            new TwigFunction('get_change_maker_active_project', [$this, 'getChangeMakerActiveProject']),
            ];
    }

    public function getCmsArticleCount($mstCategory_id, $status)
    {
        return $this->em->getRepository(CmsArticle::class)->getArticleCount($mstCategory_id, $status);
    }

    public function getCmsArticle($category_id, $company_id)
    {
        return $this->em->getRepository(CmsArticle::class)->findBy(['mstArticleCategory' => $category_id, 'orgCompany' => $company_id, 'isActive' => 1], ['articleCreateDateTime' => 'DESC']);
    }

    public function getCmsArticleLimit($category_id, $company_id, $limit = 4)
    {
        return $this->em->getRepository(CmsArticle::class)->findBy(['mstArticleCategory' => $category_id, 'orgCompany' => $company_id, 'isActive' => 1], ['articleCreateDateTime' => 'DESC', 'sequenceNo' => 'ASC'], $limit, 0);
    }

    public function getCmsArticleComment($article_id, $is_approved)
    {
        $cmsArticleComment = $this->em->getRepository(CmsArticleComment::class)->findBy(['cmsArticle' => $article_id, 'isApproved' => $is_approved], ['commentDateTime' => 'DESC']);

        if ($cmsArticleComment) {
            foreach ($cmsArticleComment as $comment) {

                $userDispName = '';
                $userImage = '';
                $userInitials = '';

                // If logged in user
                if($comment->getAppUser() != null) {
                    if ($comment->getAppUser()->getAppUserInfo()->getUserFirstName() != '') {
                        $userDispName .= $comment->getAppUser()->getAppUserInfo()->getUserFirstName() . ' ';
                    }
                    if ($comment->getAppUser()->getAppUserInfo()->getUserLastName() != '') {
                        $userDispName .= $comment->getAppUser()->getAppUserInfo()->getUserLastName() . ' ';
                    }
                    if(trim($comment->getAppUser()->getAppUserInfo()->getUserAvatarImage()) != '' &&
                        $comment->getAppUser()->getAppUserInfo()->getUserAvatarImage() != null) {
                        $userImage = $comment->getAppUser()->getAppUserInfo()->getUserAvatarImagePath();
                    }
                }

                // If anonymous user
                if(trim($userDispName) == '') {
                    $userDispName = $comment->getCommentorName();
                }

                // If no image set then use initials
                if(trim($userImage) == '') {
                    $userInitialsArr = explode(' ', $userDispName);
                    foreach ($userInitialsArr as $str) {
                        $userInitials.= ''.substr($str, 0, 1);
                    }
                }

                if (!empty($comment->getParentComment())) {
                    $parentId = $comment->getParentComment()->getId();
                    $id = $comment->getId();

                    $comments['reply'][$parentId][$id]['parentId'] = $parentId;
                    $comments['reply'][$parentId][$id]['comment'] = $comment->getArticleComment();
                    $comments['reply'][$parentId][$id]['name'] = $comment->getCommentorName();
                    $comments['reply'][$parentId][$id]['createtime'] = $comment->getCommentDateTime();
                    $comments['reply'][$parentId][$id]['name'] = trim($userDispName);
                    $comments['reply'][$parentId][$id]['userimage'] = $userImage;
                    $comments['reply'][$parentId][$id]['userinitials'] = $userInitials;
                }

                if (empty($comment->getParentComment())) {
                    $id = $comment->getId();
                    $comments['comment'][$id]['id'] = $comment->getId();
                    $comments['comment'][$id]['comment'] = $comment->getArticleComment();
                    $comments['comment'][$id]['createtime'] = $comment->getCommentDateTime();
                    $comments['comment'][$id]['likecount'] = $comment->getCommentLikeCount();
                    $comments['comment'][$id]['name'] = trim($userDispName);
                    $comments['comment'][$id]['userimage'] = $userImage;
                    $comments['comment'][$id]['userinitials'] = $userInitials;
                }
            }
        } else {
            $comments = [];
        }

        return $comments;
    }

    public function getCmsArticleCommentCount($article_id, $is_approved)
    {
        return $this->em->getRepository(CmsArticleComment::class)->getArticleCommentCount($article_id, $is_approved);
    }

    public function getContentBySlugName($slugName)
    {
        return $this->em->getRepository(CmsArticle::class)->getContentBySlugName($slugName);
    }

    public function getOtherArticlesById($category_id, $company_id, $article_id, $limit = 4)
    {
        return $this->em->getRepository(CmsArticle::class)->getOtherArticlesBySlugNameLimit($category_id, $company_id, $article_id, $limit);
    }

    public function getChangeMakerProject($changeMakerAppUser)
    {
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status"=> 'Activated']);

        return $this->em->getRepository(TrnCircle::class)->getAppUserProjects($changeMakerAppUser, $objMstStatus);
    }

    public function getChangeMakerActiveProject($changeMakerAppUser)
    {
        $objMstStatus = $this->em->getRepository(MstStatus::class)->findOneBy(["status"=> 'Activated']);

        // getAppUserProjects() include circle with cocore as well
//        return $this->em->getRepository(TrnCircle::class)->getAppUserProjects($changeMakerAppUser, $objMstStatus);
        return $this->em->getRepository(TrnCircle::class)->getAppUserActiveCircles($changeMakerAppUser, $objMstStatus);
    }

}