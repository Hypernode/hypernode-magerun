<?php
/**
 * Byte Hypernode Magerun
 *
 * @package     hypernode-Magerun
 * @author      Byte
 * @copyright   Copyright (c) 2016 Byte
 * @license     http://opensource.org/licenses/osl-3.0.php Open Software License 3.0 (OSL-3.0)
 *
 * # Notes for this command
 * @note optimize the ways of validating URLS, add ports support for staging and maybe store codes
 * @note there is an issue with selecting sitemaps using keyboard arrows, wrong input is delivered
 * @note ports and store codes are currently not supported
 */

namespace Hypernode\Magento\Command\Hypernode\Performance;

use Hypernode\Magento\Command\AbstractHypernodeCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Question\ChoiceQuestion;
use N98\Util\Console\Helper\Table\Renderer\RendererFactory;

/**
 * Class CacheWarmerCommand
 * @package Hypernode\Magento\Command\Performance
 */
class PerformanceCommand extends AbstractHypernodeCommand
{

    protected $_options;
    protected $_sitemaps;
    protected $_batches;
    protected $_results;
    protected $_totalTime = false;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('hypernode:performance')
            ->setDescription('Generate a performance report based on sitemaps.')
            ->addOption('sitemap', null, InputOption::VALUE_OPTIONAL, '(string) path or URL.', false)
            ->addOption('current-url', null, InputOption::VALUE_OPTIONAL, 'Url of current instance. (needle for replacement)', false)
            ->addOption('compare-url', null, InputOption::VALUE_OPTIONAL, 'The URL to compare with.', false)
            ->addOption('silent', null, InputOption::VALUE_NONE, 'Disables all messages, outputs results in JSON.', null)
            ->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Format for file output result [' . implode(',', RendererFactory::getFormats()) . '] (default console table)', false)
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Limits the amount of requests to curl.', false)
            ->addOption('totaltime', null, InputOption::VALUE_NONE, 'Measure total time instead of TTFB. Note: TTFB labels are not adjusted.');
    }


    /**
     * Executes command.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);
        if (!$this->initMagento()) {
            return;
        }

        // setting the options - hypernode demands it
        $this->_options = $input->getOptions();

        // finding the effective url e.g. store.nl -> https://www.store.nl
        if ($this->_options['current-url'] && $this->_options['compare-url']) {
            $this->_options['current-url'] = $this->getEffectiveUrl($this->_options['current-url']);
            $this->_options['compare-url'] = $this->getEffectiveUrl($this->_options['compare-url']);
        }

        // get sitemaps to process
        if (!$this->_options['sitemap'] && !$this->_options['silent']) {
            $this->_sitemaps = $this->askSitemapsToProcess($input, $output);
        } else {
	    if (!$this->_options['sitemap'] && $this->_options['silent']) {
	        $this->_sitemaps = $this->retrieveSitemaps();
            } else {
                $sitemapFromInput = $this->getSitemapFromInput($this->_options);
                if (!$sitemapFromInput) {
                    $output->writeln('<error>Could not fetch specified sitemap: ' . $this->_options['sitemap'] . '</error>');
                } else {
                    $this->_sitemaps = $sitemapFromInput;
                }
            }
         
        }

        // prepare the requests
        if ($this->_sitemaps) {
            $this->_batches = $this->prepareRequests($input, $output);
        }

        // execute the requests
        if ($this->_batches) {
            $this->_results = $this->executeBatches($input, $output);
        }

        // serve the results
        if ($this->_results) {

            if ($this->_options['silent'] && !$this->_options['format']) {
                $output->write(json_encode($this->_results) . PHP_EOL); // hypernode internal
            } else {
                $tableHelper = $this->getHelper('table');

                if ($this->_options['format']) { // user specified format
                    $tableData = $this->generateTablesDataForFormat($this->_results);
                } else {
                    $tableData = $this->generateTablesData($this->_results);
                }

                foreach ($tableData as $data) {

                    if (!$this->_options['silent']) {
                        $this->writeSection($output, "Performance status report - [Byte Hypernode]");
                    }

                    $tableHelper->setHeaders($data['headers']);
                    if ($this->_options['format']) {
                        $tableHelper->renderByFormat($output, $data['requests'], $this->_options['format']);
                    } else {
                        $tableHelper->renderByFormat($output, $data['requests']);
                    }
                }
            }
        }


    }


    /**
     * Gets the effective URL by following the redirects.
     * @todo change to curl Class
     * @param $url
     * @return mixed
     */
    protected function getEffectiveUrl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        # Identify as a known crawler so we don't bypass the Varnish cache on shops with magento-turpentine 0.1.6 or later
        # https://github.com/nexcess/magento-turpentine/blob/e3577315cdd8fb35b1bff812d2cf1b61e1b76c13/CHANGELOG.md#release-016
        # https://github.com/nexcess/magento-turpentine/blob/e3577315cdd8fb35b1bff812d2cf1b61e1b76c13/app/code/community/Nexcessnet/Turpentine/etc/config.xml#L66
        curl_setopt($ch, CURLOPT_USERAGENT, "ApacheBench/2.3");
        curl_exec($ch);
        return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    }

    /**
     * Strips the domain to only domain name and tld.
     * @note no schema, subdomain, port or path
     * @param $url
     * @return bool
     */
    protected function getStrippedUrl($url)
    {
        $pattern = '/\w+\..{2,3}(?:\..{2,3})?(?:$|(?=\/))/i';
        return preg_match($pattern, $url, $matches) === 1 ? $matches[0] : false;
    }

    /**
     * Generates table data without console styling.
     * @note currently only change of dataset, future also different formats without table helper to save in file.
     * @param $results
     * @return array
     */
    protected function generateTablesDataForFormat($results)
    {
        $tables = array(); // all tables
        foreach ($results as $set) {
            $tableArray = array(
                'headers' => false,
                'requests' => array()
            ); // every table row

            if (count($set[0]) > 1) {
                $tableArray['headers'] = array("current_url", "current_url_status", "current_url_response", "compare_url", "compare_url_status", "compare_url_response", "difference");
            } elseif (count($set[0]) == 1) {
                $tableArray['headers'] = array("url", "status", "response");
            }

            foreach ($set as $batch) {
                $requestArray = array();
                if (count($batch) > 1) {
                    foreach ($batch as $request) {
                        $requestArray[] = $request['url'];
                        $requestArray[] = $request['status'];
                        $requestArray[] = $request['ttfb'];
                    }
                    $requestArray[] = $batch[0]['ttfb'] - $batch[1]['ttfb'];
                } elseif (count($batch) == 1) {
                    $requestArray[] = $batch[0]['url'];
                    $requestArray[] = $batch[0]['status'];
                    $requestArray[] = $batch[0]['ttfb'];
                }
                array_push($tableArray['requests'], $requestArray);
            }
            array_push($tables, $tableArray);
        }
        return $tables;
    }


    /**
     * Generates data to output in a table.
     * @todo make table nicer, fix colorized output in diffferent format - new function
     * @param $results
     * @return array|bool
     */
    protected function generateTablesData($results)
    {
        $tables = array(); // all tables
        foreach ($results as $set) { // foreach sitemap we parsed

            // setting placeholder var and headers
            $tableArray = array(
                'headers' => false,
                'requests' => array()
            ); // every table row

            // setting headers
            if (count($set[0]) > 1) {
                $tableArray['headers'] = array("URL", "Status X", "TTFB X", "Status Y", "TTFB Y", "Difference");
            } elseif (count($set[0]) == 1) {
                $tableArray['headers'] = array("URL", "Status", "TTFB");
            }

            foreach ($set as $batch) {

                $requestArray = array();

                if (count($batch) > 1) {
                    $parsedUrl = parse_url($batch[0]['url']);
                    $requestArray[] = $parsedUrl['path'];
                    foreach ($batch as $request) {
                        $requestArray[] = $this->parseResponseCode($request['status']);
                        $requestArray[] = $request['ttfb'];
                    }
                    $requestArray[] = $this->ttfbCompare($batch[0]['ttfb'], $batch[1]['ttfb']);
                } elseif (count($batch) == 1) {

                    $requestArray[] = $batch[0]['url'];
                    $requestArray[] = $this->parseResponseCode($batch[0]['status']);
                    $requestArray[] = $batch[0]['ttfb'];
                } else {
                    return false; // output data as json or something
                }
                array_push($tableArray['requests'], $requestArray);
            }
            array_push($tables, $tableArray);
        }
        return $tables;
    }


    /**
     * Executes all prepared batches of requests.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    protected function executeBatches(InputInterface $input, OutputInterface $output)
    {
        $totalResult = array();

        if (!$this->_options['silent']) {
            $output->writeln('<info>Found ' . count($this->_batches) . ' sets to process.</info>');
        }

        $setCount = count($this->_batches);
        $bi = 1; // batch number

        foreach ($this->_batches as $set) { // sitemaps
            $meta = $set['metadata'];
            $batchesCount = count($set['requests']);

            $setResults = array();
            if (!$this->_options['silent']) {
                $progress = new ProgressBar($output, $batchesCount);
                $progress->setFormat('<info> %message% </info>' . PHP_EOL . '%current%/%max% [%bar%] <comment> %percent:3s%% - %elapsed:6s%/%estimated:-6s% </comment>');
                $progress->setMessage('Now executing batch: ' . $bi . '/' . $setCount);
                $progress->start();
            }

            foreach ($set['requests'] as $batch) { // the batches of requests, singular or plural
                $batchResult = array();
                foreach ($batch as $request) {
                    $response = $this->simpleCurl($request);
                    $response['comparison_key'] = strtr($response['url'], array(rtrim($this->_options['compare-url'], "/") => '', rtrim($this->_options['current-url'], "/") => ''));
                    array_push($batchResult, $response);
                }
                array_push($setResults, $batchResult);

                if (!$this->_options['silent']) {
                    $progress->clear();
                    $progress->setMessage($this->parseStatusMessage($batchResult));
                    $progress->display();
                    $progress->advance();
                }
            }

            if (!$this->_options['silent']) {
                $progress->setMessage('Task is finished');
                $progress->finish();
            }
            array_push($totalResult, $setResults);
            $bi++;
        }

        return $totalResult;
    }

    /**
     * Parses the status message for the user in between requests.
     * @param $result
     * @return string
     */
    protected function parseStatusMessage($result)
    {
        $message = '';
        if (count($result) > 1) { // compare
            $parsedUrl = parse_url($result[0]['url']);

            $message .= "<fg=white>URL:</> " . $parsedUrl['path'] . ' | ';
            $message .= " <fg=white>Status:</> " . $this->parseResponseCode($result[0]['status']) . "/" . $this->parseResponseCode($result[1]['status']) . " | ";
            $message .= " <fg=white>TTFB:</> " . $result[0]['ttfb'] . ' / ' . $result[1]['ttfb'] . ' | <fg=white>Difference:</> ' . $this->ttfbCompare($result[0]['ttfb'], $result[1]['ttfb']);
            return $message;
        } else { // single
            $message .= "URL: " . '<fg=white>' . $result[0]['url'] . '</>';
            $message .= " " . $this->parseResponseCode($result[0]['status']);
            $message .= " TTFB: " . $result[0]['ttfb'];
            return $message;
        }
    }

    /**
     * Compare the TTFB Values and give colorized response if necessary.
     * @param $new
     * @param $old
     * @return string
     */
    protected function ttfbCompare($new, $old)
    {
        $difference = $new - $old;
        if ($difference > 0) {
            $difference = "<fg=red>" . $difference . "</>";
        }
        return $difference;
    }

    /**
     * Parses the response code colorized.
     * @param $code
     * @return bool|string
     */
    protected function parseResponseCode($code)
    {
        switch ($code) {
            case '200':
                $response = "<fg=green>" . $code . "</>";
                break;
            case '404':
            case '500':
                $response = "<fg=red>" . $code . "</>";
                break;
            default:
                $response = "<fg=blue>" . $code . "</>";
        }
        return $response;
    }

    /**
     * A simple Curl function used to process the requests.
     * @param $url
     * @return array
     */
    protected function simpleCurl($url)
    {
        $result = array();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        # Identify as a known crawler so we don't bypass the Varnish cache on shops with magento-turpentine 0.1.6 or later
        # https://github.com/nexcess/magento-turpentine/blob/e3577315cdd8fb35b1bff812d2cf1b61e1b76c13/CHANGELOG.md#release-016
        # https://github.com/nexcess/magento-turpentine/blob/e3577315cdd8fb35b1bff812d2cf1b61e1b76c13/app/code/community/Nexcessnet/Turpentine/etc/config.xml#L66
        curl_setopt($ch, CURLOPT_USERAGENT, "ApacheBench/2.3");
        curl_exec($ch);

        $result['url'] = $url;
        $result['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $result['ttfb'] = curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME);
        if (!$this->_totalTime) {
            $result['ttfb'] = curl_getinfo($ch, CURLINFO_STARTTRANSFER_TIME);
        } else {
            $result['ttfb'] = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        }

        curl_close($ch);
        return $result;
    }

    /**
     * Prepares all requests data by processing (sitemap) input.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function prepareRequests(InputInterface $input, OutputInterface $output)
    {
        $requestSetCollection = array(); // collection of all sets of requests (set = sitemap)

        // looping through all sitemaps, get, validate and prepare them
        foreach ($this->_sitemaps as $sitemap) {
            $requestSet = array(); // a sitemap
            $requestSet['metadata'] = $sitemap;

            $xml = '';

            // Getting the XML from URL & validate it
            if (isset($sitemap['sitemap_url'])) {
                if ($this->validateUrl($sitemap['sitemap_url'])) {
                    try {
                        $curl = $this->getCurl();
                        $curl->get($sitemap['sitemap_url']);
                        if ($curl->http_status_code == '200') {
                            try {
                                $xml = new \Varien_Simplexml_Element($curl->response);
                            } catch (\Exception $e) {
                                $output->writeln('<error>' . $e->getMessage() . ' ' . $sitemap['sitemap_url'] . '</error>');
                                continue;
                            }
                        }
                    } catch (\Exception $e) {
                        $output->writeln('<error>An error occured while getting the sitemap: ' . $e->getMessage() . '</error>');
                        continue;
                    }
                } else {
                    $output->writeln('<error>The URL: ' . $sitemap['sitemap_url'] . ' is not valid.</error>');
                    continue;
                }

                // getting the sitemap from a file
            } else {
                if (file_exists($this->_magentoRootFolder . $sitemap['relative_path'])) {
                    try {
                        $xml = new \Varien_Simplexml_Element(file_get_contents($this->_magentoRootFolder . $sitemap['relative_path']));
                    } catch (\Exception $e) {
                        $output->writeln('<error>' . $e->getMessage() . ' ' . $sitemap['relative_path'] . '</error>');
                        continue;
                    }

                    // converting a txt of urls to magento sitemap structure (hypernode internal)
                } elseif (file_exists($sitemap['relative_path'])) {
                    if (end(explode('.', $sitemap['relative_path'])) == 'txt') {
                        $xml = new \Varien_Simplexml_Element($this->convertTxtToXml(file_get_contents($sitemap['relative_path'])));
                    } else {
                        $output->writeln('<error>Only a txt url list is currently supported for absolute paths.</error>');
                    }
                }
            }

            // creating batches
            if ($xml) {

                $requestSet['requests'] = array();
                $urls = array();

                foreach ($xml->children() as $child) {
                    array_push($urls, $child->loc);
                }

                $replace = false;
                // finding out which replace strategy to use
                if ($this->_options['sitemap'] && $this->_options['compare-url'] && $this->_options['current-url']) {
                    $replace = 3; // Replace and compare
                } elseif ($requestSet['metadata']['base_url']) {
                    if (!$this->matchUrls($requestSet['metadata']['base_url'], $urls[0])['status']) {
                        $replace = $this->askReplaceOrCompare($input, $output, $requestSet['metadata']['base_url'], $urls[0]);
                    }
                }

                $i = 1;
                foreach ($urls as $url) {
                    $requestBatch = array(); // batch for curling

                    // replace strategy execution
                    if ($replace) {
                        if ($replace == 1) { // Use site from sitemap
                            array_push($requestBatch, $this->replaceUrlByParse($url, $requestSet['metadata']['base_url']));
                        } elseif ($replace == 2) { // Use both (side by side)
                            array_push($requestBatch, $this->replaceUrlByParse($url, $requestSet['metadata']['base_url'])); //left
                            array_push($requestBatch, (string)$url); // right
                        } elseif ($replace == 3) {
                            array_push($requestBatch, $this->replaceUrl($url, $this->_options['current-url']));
                            array_push($requestBatch, $this->replaceUrl($url, $this->_options['compare-url']));
                        } else {
                            array_push($requestBatch, (string)$url);
                        }
                    } else {
                        array_push($requestBatch, (string)$url); // no replace, just crawl
                    }

                    array_push($requestSet['requests'], $requestBatch);

                    if ($this->_options['limit'] && $i >= $this->_options['limit']) {
                        break;
                    } else {
                        $i++;
                    }
                } //endforeach
            } //endif $xml @todo verify that no empty set is returned with bad $xml (prio)

            array_push($requestSetCollection, $requestSet);
        }
        return $requestSetCollection;
    }

    /**
     * Convert a txt URL list to XML using Magento sitemap structure.
     * @note could be used for auto generating if no sitemaps available
     * @param $txt
     * @return string
     */
    protected function convertTxtToXml($txt)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach (explode("\n", $txt) as $url) {
            $xml .= '<url><loc>' . $url . '</loc></url>';
        }
        $xml .= '</urlset>';
        return $xml;
    }

    /**
     * Replaces the URL by a regex strip.
     * @param $sitemapUrl
     * @param $replaceUrl
     * @return string
     */
    protected function replaceUrl($sitemapUrl, $replaceUrl)
    {
        $strippedUrl = $this->getStrippedUrl($sitemapUrl);
        $parts = explode($strippedUrl, $sitemapUrl);
        $url = rtrim($replaceUrl, "/");
        $path = ltrim(end($parts), "/");
        return $url . "/" . $path;
    }

    /**
     * Replaces the Sitemap URL
     * @todo finish port support
     * @param $sitemapUrl
     * @param $replaceUrl
     * @return mixed
     */
    protected function replaceUrlByParse($sitemapUrl, $replaceUrl)
    {
        $toReplace = $this->matchUrls($sitemapUrl, $replaceUrl)['mismatches'];
        foreach ($toReplace as $replacement) {
            if ($replacement === "port") {
                // @todo output error that ports are not supported yet
            } else {
                $sitemapUrl = str_replace(parse_url($sitemapUrl)[$replacement], parse_url($replaceUrl)[$replacement], $sitemapUrl);
            }
        }
        return (string)$sitemapUrl;
    }

    /**
     * Asks the user what to do in case of a mismatched URL
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $definedUrl
     * @param $foundUrl
     * @return bool|int
     */
    protected function askReplaceOrCompare(InputInterface $input, OutputInterface $output, $definedUrl, $foundUrl)
    {
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            '<question>The identified URL: ' . parse_url($definedUrl)['host'] . ', does not match the found URL: ' . parse_url($foundUrl)['host'] . ' by: ' . implode(" ,", $this->matchUrls($definedUrl, $foundUrl)['mismatches']) . PHP_EOL . 'What do you want to do?</question>',
            array('Use site from sitemap', 'Use site from default url', 'Use both (side by side)')
        );
        $answer = $helper->ask($input, $output, $question);
        $output->writeln($answer);

        switch ($answer) {
            case 'Use site from default url':
                $answer = 1;
                break;
            case 'Use both (side by side)':
                $answer = 2;
                break;
            default:
                $answer = false;
        }
        return $answer;
    }

    /**
     * Checks if URL's are identical.
     * @note does not support store code or port usage
     * @param $url
     * @param $match
     * @return mixed
     */
    protected function matchUrls($url, $match)
    {
        $parsedUrl = parse_url($url);
        $parsedMatchUrl = parse_url($match);

        $matchResult = array(
            'status' => true,
            'mismatches' => array()
        );

        // check schema - http / https
        if ($parsedUrl['scheme'] !== $parsedMatchUrl['scheme']) {
            $matchResult['status'] = false;
            array_push($matchResult['mismatches'], 'scheme');
        }

        // check host, www / non-www etc.
        if ($parsedUrl['host'] !== $parsedMatchUrl['host']) {
            $matchResult['status'] = false;
            array_push($matchResult['mismatches'], 'host');
        }

        // check port, e.g. staging
        if (isset($parsedUrl['port']) || isset($parsedMatchUrl['port'])) {
            if ($parsedUrl['port'] !== $parsedMatchUrl['port']) {
                $matchResult = false;
                array_push($matchResult['mismatches'], 'port');
            }
        }
        return $matchResult;
    }

    /**
     * Processes the sitemap data from input.
     * @note multi sitemap and fetching sitemap by Int not supported atm.
     * @todo Support multi sitemap and fetching sitemap by int
     * @param $options
     * @return array|bool
     */
    protected function getSitemapFromInput($options)
    {
        $sitemaps = array();
        if ($this->validateUrl($options['sitemap'])) {
            try {
                $curl = $this->getCurl();
                $curl->get($options['sitemap']);
                if ($curl->http_status_code == '200') {
                    $parsedUrl = parse_url($options['sitemap']);
                    array_push($sitemaps, array(
                        'relative_path' => $parsedUrl['path'],
                        'sitemap_url' => $options['sitemap'],
                        'base_url' => $options['current-url']
                    ));
                } else {
                    return false;
                }
            } catch (\Exception $e) {
                if (!$this->_options['silent']) {
                    throw new \RuntimeException('Could not fetch a sitemap at ' . $options['sitemap'] . ' .');
                }
                return false;
            }
        } else {
            $str = $options['sitemap'];

            if (substr($options['sitemap'], 0, 1) != '/') {
                $str = DIRECTORY_SEPARATOR . $str;
                $pathType = 'relative';
            } else {
                $pathType = 'absolute';
            }

            if ($pathType == 'relative') {
                if (file_exists($this->_magentoRootFolder . $str)) {
                    array_push($sitemaps, array(
                        'relative_path' => $str,
                        'base_url' => $options['current-url']
                    ));
                } else {
                    return false;
                }
            } elseif ($pathType == 'absolute') {
                if (file_exists($str)) {
                    array_push($sitemaps, array(
                        'relative_path' => $str,
                        'base_url' => $options['current-url']
                    ));
                } else {
                    return false;
                }
            }
        }
        return $sitemaps;
    }

    /**
     * Retrieve the sitemapCollection
     * @return array
     */
    protected function retrieveSitemaps()
    {
        $sitemaps = array();
        $sitemapCollection = $this->getStoreSitemaps();
        return $sitemapCollection;
    }

    /**
     * Asks the user which sitemaps from Magento's sitemap collection to process.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array|bool
     */
    protected function askSitemapsToProcess(InputInterface $input, OutputInterface $output)
    {
        $sitemapCollection = $this->retrieveSitemaps();

        if (!$sitemapCollection) {
            $output->writeln('<error>There are no sitemaps defined in Magento\'s sitemap collection.</error>');
            return false;
        }

        $this->getHelper('table')
            ->setHeaders(array('Store ID', 'Active', 'Store code', 'Path', 'Generated', 'Store URL'))
            ->renderByFormat($output, $sitemapCollection);

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            '<question>Please select one or more sitemaps. - use numbers, comma seperated for multi</question>',
            array_column($sitemapCollection, 'relative_path')
        );
        $question->setMultiselect(true);

        $answer = $helper->ask($input, $output, $question);

        foreach ($sitemapCollection as $sitemap) {
            if (in_array($sitemap['relative_path'], $answer)) {
                $sitemaps[] = $sitemap;
            }
        }
        return $sitemaps;
    }


    /**
     * Gets the store sitemap collection.
     * @return array|bool
     * @throws \Mage_Core_Exception
     */
    protected function getStoreSitemaps()
    {
        $sitemaps = array();
        $collection = \Mage::getModel('sitemap/sitemap')->getCollection();

        foreach ($collection as $item) {
            $store = \Mage::getModel('core/store')->load($item->getStoreId());
            $sitemap['store_id'] = $item->getStoreId();
            $sitemap['store_active'] = $store->getIsActive() ? 'Yes' : 'No';
            $sitemap['store_code'] = $store->getCode();
            $sitemap['relative_path'] = $item->getSitemapPath() . $item->getSitemapFilename();
            $sitemap['sitemap_time'] = $item->getSitemapTime();
            $sitemap['base_url'] = \Mage::app()->getStore($sitemap['store_id'])->getBaseUrl(\Mage_Core_Model_Store::URL_TYPE_LINK);
            array_push($sitemaps, $sitemap);
        }
        return empty($sitemaps) ? false : $sitemaps;
    }

    /**
     * Validates an URL
     * @param $url
     * @return bool
     */
    protected function validateUrl($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) ? true : false;
    }

}
