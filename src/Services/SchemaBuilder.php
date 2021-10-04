<?php
/**
 * User: Tuhin
 * Date: 2/16/2018
 * Time: 11:34 PM
 */

namespace SEO\Services;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Locale;
use SEO\Models\Page;
use SEO\Models\Setting;

class SchemaBuilder
{
    /**
     * @var Collection
     */
    protected $settings = [];

    protected $socialAccounts = [];

    public function __construct()
    {
        $this->settings = Setting::where('group', 'ownership')->pluck('value', 'key')->toArray();
        $this->socialAccounts = Setting::where('group', 'social_media_links')->pluck('value', 'key')->toArray();
    }

    /**
     * @return null|string
     */
    public function ownership(Page $page)
    {
        $info = $this->buildOwnerShip($page);
        return !empty($info) ? json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null;
    }

    /**
     * @return array|void
     */
    private function buildOwnerShip(Page $page)
    {
        $arr = [];
        $arr['@context'] = 'https://schema.org';

        $graphs = [];

        // Person / Organization
        if (!empty($this->settings['ownership_type'])) {
            $ownershipGraph = [];
            $ownershipGraph['@type'] = $this->settings['ownership_type'];
            $ownershipGraph['name'] = $this->settings['ownership_name'];
            $ownershipGraph['url'] = $this->settings['ownership_url'];
            if (!empty($this->settings['review_rating_value'])) {
                $ownershipGraph['aggregateRating'] = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => $this->settings['review_rating_value'] ?? 10,
                    'reviewCount' => $this->settings['review_count'] ?? 112,
                    'worstRating' => $this->settings['review_worst_rating'] ?? 1,
                    'bestRating' => $this->settings['review_best_rating'] ?? 10
                ];
            }
            $ownershipGraph = $this->logo($ownershipGraph);
            if (!empty($this->settings['ownership_address'])) {
                $ownershipGraph['address'] = $this->settings['ownership_address'];
            }
            if (!empty($this->settings['ownership_email']) && filter_var($this->settings['ownership_email'], FILTER_VALIDATE_EMAIL)) {
                $ownershipGraph['email'] = $this->settings['ownership_email'];
            }
            $ownershipGraph['sameAs'] = $this->getSocialMediaLinks();
            $ownershipGraph = $this->telephone($ownershipGraph);
            $graphs[] = $ownershipGraph;
        }

        // Article
        $articleGraph = $this->buildArticle($page);
        if (!empty($articleGraph)) {
            $graphs[] = $articleGraph;
        }

        // Generate breadcrumbs
        $breadcrumbsGraph = $this->buildBreadcrumbs($page);
        if (!empty($breadcrumbsGraph)) {
            $graphs[] = $breadcrumbsGraph;
        }

        // Faq
        $faqGraph = $this->buildFaq($page);
        if (!empty($faqGraph)) {
            $graphs[] = $faqGraph;
        }

        $arr['@graph'] = $graphs;

        return $arr;
    }

    /**
     * @return array
     */
    private function getSocialMediaLinks()
    {
        $retArr = [];

        foreach ($this->socialAccounts as $sm => $url) {
            if (!empty($url)) {
                $retArr[] = $url;
            }
        }
        return $retArr;
    }

    /**
     * @param $arr
     * @return mixed
     */
    private function telephone($arr)
    {
        if (isset($this->settings['ownership_contact_point_telephone']) && !empty($this->settings['ownership_contact_point_telephone'])) {
            if ($this->settings['ownership_type'] == 'Organization') {
                $arr['contactPoint'] = [
                    [
                        "@type" => "ContactPoint",
                        'telephone' => $this->settings['ownership_contact_point_telephone'],
                        'contactType' => $this->settings['ownership_contact_point_contact_type']
                    ]
                ];
            } else {
                $arr['telephone'] = $this->settings['ownership_contact_point_telephone'];
            }
        }
        return $arr;
    }

    /**
     * @param $arr
     * @return array
     */
    private function logo($arr)
    {
        if (isset($this->settings['ownership_logo']) && !empty($this->settings['ownership_logo']) && filter_var($this->settings['ownership_logo'], FILTER_VALIDATE_URL)) {
            if ($this->settings['ownership_type'] == 'Organization') {
                $arr['logo'] = $this->settings['ownership_logo'];
            } else {
                $arr['image'] = $this->settings['ownership_logo'];
            }
        }
        return $arr;
    }

    /**
     * @param Page $page
     * @return array
     */
    private function buildArticle(Page $page)
    {
        $article = [];
        if (!empty($page->getTitle()) && !empty($page->getDescription()) && !empty($this->settings['ownership_type'])) {
            $article["@type"] = "Article";
            $article["mainEntityOfPage"] = [
                "@type" => "WebPage",
                "@id" => $page->getFullUrl()
            ];
            $article["headline"] = $page->getTitle();
            $article["description"] = $page->getDescription();
            $image = $page->pageImages()->first();
            if ($image) {
                $article["image"] = $image->getSrc();
            }
            $article["author"] = [
                "@type" => $this->settings['ownership_type'],
                "name" => $this->settings['ownership_name'],
                "url" => $this->settings['ownership_url']
            ];
            $article["publisher"] = [
                "@type" => $this->settings['ownership_type'],
                "name" => $this->settings['ownership_name']
            ];
            if (isset($this->settings['ownership_logo']) && !empty($this->settings['ownership_logo']) && filter_var($this->settings['ownership_logo'], FILTER_VALIDATE_URL)) {
                $article["publisher"]["logo"] = [
                    "@type" => "ImageObject",
                    "url" => $this->settings['ownership_logo']
                ];
            }
            $article["datePublished"] = $page->getCreatedDate();
            $article["dateModified"] = $page->getLastModifiedDate();
        }
        return $article;
    }

    /**
     * @param Page $page
     * @return array
     */
    private function buildBreadcrumbs(Page $page)
    {
        /**
         * {
        "@context": "https://schema.org/",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "",
            "item": ""
        },{
            "@type": "ListItem",
            "position": 2,
            "name": "",
            "item": ""
        }]
        }*/
        $breadcrumbs = [
            "@type" => "BreadcrumbList",
            "itemListElement" => []
        ];
        if (!empty($page->getTitle()) && !empty($page->getDescription()) && !empty($this->settings['ownership_type'])) {
            $breadcrumbs["itemListElement"][] = [
                "position" => 1,
                "name" => "Home",
                "item" => "/"
            ];
            $breadcrumbs["itemListElement"][] = [
                "position" => 2,
                "name" => Locale::getDisplayName(Lang::locale(), Lang::locale()),
                "item" => url('/'.Lang::locale())
            ];
        }
        return count($breadcrumbs["itemListElement"]) ? $breadcrumbs : [];
    }

    /**
     * @param Page $page
     * @return array
     */
    private function buildFaq(Page $page)
    {
        /**
         * {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [{
                "@type": "Question",
                "name": "ddd",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "ddd"
                }
                },{
                "@type": "Question",
                "name": "",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": ""
                }
            }]
        }
         */
        $faq = [
            "@type" => "FAQPage",
            "mainEntity" => []
        ];
        for($i = 0; $i < 10; $i++) {
            if (!empty($this->settings['faq_question_'.$i]) && !empty($this->settings['faq_answer_'.$i])) {
                $faq["mainEntity"][] = [
                    "@type" => "Question",
                    "name" => $this->settings['faq_question_'.$i],
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => $this->settings['faq_answer_'.$i]
                    ]
                ];
            }
        }
        return count($faq["mainEntity"]) ? $faq : [];
    }

}
