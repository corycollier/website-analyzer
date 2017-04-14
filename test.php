<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use WebsiteAnalyzer\ListBuilder;

// Define all of the constants
define('UNFILTERED_SITES_FILENAME', 'list-of-sites.txt');
define('GOOD_SITES_FILENAME', 'good-sites.txt');
define('BAD_SITES_FILENAME', 'bad-sites.txt');
define('CATEGORIZED_SITES_FILENAME', 'categorized-sites.txt');

$builder = new ListBuilder('test.yml');
$builder->process();
die;




$unfiltered = get_unfiltered_sites();
$good_sites        = get_good_sites();
$bad_sites         = get_bad_sites();
$categorized_sites = get_categorized_sites();

print_r([
  'unfiltered' => $unfiltered,
  'bad' => $bad_sites,
  'good' => $good_sites,
  'categorized' => $categorized_sites,
]);

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function get_categorized_sites() {
  if (!file_exists(CATEGORIZED_SITES_FILENAME)) {
    $results = [];
    $sites = get_good_sites();
    $client  = new Client;

    foreach ($sites as $site) {
      $response = $client->request('GET', $site);

      if (is_drupal($response)) {
        $results['drupal'][] = $site;
        continue;
      }

      if (is_wordpress($response)) {
        $results['wordpress'][] = $site;
        continue;
      }

      if (is_static($response)) {
        $results['static'][] = $site;
      }
    }
    file_put_contents(CATEGORIZED_SITES_FILENAME, serialize($results));
  }
  return unserialize(file_get_contents(CATEGORIZED_SITES_FILENAME));
}

function get_bad_sites() {
  if (!file_exists(BAD_SITES_FILENAME)) {
    $sites = get_unfiltered_sites();
    unset($sites[200]);
    file_put_contents(BAD_SITES_FILENAME, serialize($sites));
  }
  return unserialize(file_get_contents(BAD_SITES_FILENAME));
}

/**
 * Gets a list of good website uris
 * @return array  The list of good website uris
 */
function get_good_sites() {
  // If we don't already have a clean list of sites, make one
  if (!file_exists(GOOD_SITES_FILENAME)) {
    $sites = get_unfiltered_sites();
    write_to_file(GOOD_SITES_FILENAME, $sites[200]);
  }
  return get_file_lines(GOOD_SITES_FILENAME);
}

/**
 * Gets cleaned lines from a filename
 * @param  string  $filename the name of the file with the lines
 * @return array            a clean list of data
 */
function get_file_lines($filename) {
  return array_map('trim', file($filename));
}

/**
 * Retrieves a full list of unfiltered sites from a filename
 * @return array  an array of sites, ordered by status code
 */
function get_unfiltered_sites() {
  $sites = get_file_lines(UNFILTERED_SITES_FILENAME);
  $client  = new Client;
  $results = [];

  foreach ($sites as $i => $site) {
    list($status, $site) = get_full_site_uri($client, $site);
    if ($site) {
      $results[$status][] = [
        'uri' => $site,
      ];
    }
  }

  return $results;
}

/**
 * Determine the full website uri from a domain name
 * @param  Client $client The client to use for requests
 * @param  string  $site   The domain name of the site
 * @return array an array of status code and uri
 */
function get_full_site_uri(Client $client, $site) {
  if (! $site) {
    return [null, false];
  }
  foreach (['http', 'https'] as $prefix) {
    $uri = $prefix . '://' . $site;
    echo $uri, PHP_EOL;
    try {
      $response = $client->request('GET', $uri);
      return [$response->getStatusCode(), $uri];
    } catch (ConnectException $exception) {
      return ['cannot-resolve-dns', $uri];
    } catch (ClientException $exception) {
      echo $exception->getMessage();
      return [403, $uri];
    } catch (ServerException $exception) {
      echo $exception->getMessage();
      return [500, $uri];
    }
  }
  return [null, false];
}

/**
 * Writes a list of data to a file
 * @param  string  $filename the filename to write to
 * @param  array  $data     The list of data to write
 * @return boolean            The result of file_write_contents
 */
function write_to_file($filename, $data) {
  $results = [];
  foreach ($data as $datum) {
    $results[] = $datum['uri'];
  }
  return file_put_contents($filename, implode(PHP_EOL, $results));
}


function is_drupal(ResponseInterface $response) {
  $contents = $response->getBody();
  $result = preg_match('/Drupal/', $contents);
  return $result;
}

function is_wordpress(ResponseInterface $response) {
  $contents = $response->getBody();
  $result = preg_match('/Wordpress/', $contents);
  return $result;
}

function is_static(ResponseInterface $response) {
  $contents = $response->getBody();
  $result = preg_match('/assets\/css/', $contents);
  return $result;
}
