<?php

namespace App\Controller;

use DOMDocument;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlSubdomains;
use Spatie\Crawler\CrawlProfile;

use Symfony\Component\HttpFoundation\Response;
use \Spatie\Crawler\CrawlObserver;


class HomeController
{
    public function number()
    {
        return new Response(
        Crawler::create()
        ->setCrawlObserver(new PageCrawlObserver())
        ->setCrawlProfile(new CrawlSubdomains('https://batunet.com'))
        ->startCrawling('https://batunet.com')
        );
        
    }

}

class PageCrawlObserver extends CrawlObserver
{
    private $pages =[];

    /**
     * Called when the crawler has crawled the given url successfully.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        $path = $url->getPath();
        $doc = new DOMDocument();
        @$doc->loadHTML($response->getBody());
        //$title = $doc->getElementsByTagName("title")[0]->nodeValue;
        $title = $doc->getElementsByTagName("a");
        
        foreach($title as $t)   {
            
        
        if (filter_var($t->getAttribute('href'), FILTER_VALIDATE_URL) !== false) {
            if (!in_array($t->getAttribute('href'), $this->pages)){
            $parse = parse_url($t->getAttribute('href'));
            if($parse['host']=="www.batunet.com")
            array_push($this->pages,$t->getAttribute('href')); 
            }
        }

    }

       
    }

    /**
     * Called when the crawler had a problem crawling the given url.
     *
     * @param \Psr\Http\Message\UriInterface $url
     * @param \GuzzleHttp\Exception\RequestException $requestException
     * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
     */
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        //echo 'failed';
    }

    public function finishedCrawling()
    {
        dd($this->pages);
    }
}