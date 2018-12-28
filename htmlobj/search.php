<?php

/*
DEMO: http://demo.hawksearch.net/?keyword=jackets&it=item&mpp=30
LOGIN: https://dev.hawksearch.net/Login.aspx
*/

class HawkSearch {
  public static $proxy_url = PROXY_URL;
  public static $engine_name = null;
  public static $engine_api_key = null;
  public static $environment = null;
  public static $params = null;
  private static $instance = null;
  private static $output = null;
  private static $caller = null;
  private static $keyword = null;
  private static $no_results = null;

  private static $api_body = null;
  private static $api_header = null;
  private static $api_code = null;
  private static $api_url = null;
  private static $api_type = null;
  private static $api = null;
  private static $debug_message = [];

  private static function init()
  {

    self::$engine_name = ENGINE_NAME;
    self::$engine_api_key = ENGINE_KEY;
    self::$environment = ENGINE_ENV;

    self::$output = new StdClass();
    self::$output->success = false;
    self::$output->proxy_url = self::$proxy_url;
    self::$output->engine_name = self::$engine_name;
    self::$output->environment = self::$environment;
    self::$output->response = null;
    self::$output->api_uri = null;
    self::$output->no_results = true;

  }

  private static function set($type, $params = null) {
    // htmlobj url
    self::$api_url = "http://".self::$environment.".hawksearch.net/sites/".self::$engine_name."/?hawkoutput=html";
    self::$api_type = "item";

    if (!empty($params)) {
      self::$output->params = (object)$params;
      foreach ($params as $key=>$value) {
        if ($key !== 'lpurl') { // this breaks htmlobj for some reason
          //if ($key !== 'json') {  // custom data route
          if ($key == 'q' && strpos(self::$api_url, 'keyword') == false) {
            self::$api_url .= "&keyword=" . urlencode($value);
            self::$keyword = urlencode($value);
          } else {
            if ($key == 'keyword') {
              self::$keyword = urlencode($value);
            }
            self::$api_url .= "&" . $key . "=" . urlencode($value);
          }

          if ($key == "it") {
            self::$api_type = $value;
          }
          //}
        }
      }
    }

    self::$output->api_uri = self::$api_url;
    self::$output->api_type = self::$api_type;
    self::$caller = strtoupper($type);
  }


  public static function search ($params = null) {
    self::proc('SEARCH', $params);

    // return output
    return self::$output;
  }

  public static function proxy ($params = null) {
    self::proc('PROXY', $params);

    return self::$api_body;
  }

  private static function proc($type, $params = null) {
    // see if current obj has been instantialized if so fetch it singleton style
    self::getInstance();

    // init and environment variables
    self::init();

    // set user entries
    self::set($type, $params);

    // fetch api
    self::fetch();

    // success or no - testing criterias
    if (self::validate_response()) {
      // assign api data to class
      $r = new ApiModel();
      $r->set(self::$api_body);

      self::$output->api = $r;

      self::$output->no_results = (strlen($r->Results) < 1000)? true : false;
    }

    self::$output->api_message = self::$debug_message;
  }

  private static function fetch() {
    self::$api_code = null;
    self::$api_header = null;
    self::$api_body = null;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::$api_url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'HTTP_TRUE_CLIENT_IP: '.$_SERVER['REMOTE_ADDR'],
    ));

    $data = curl_exec($ch); // data = raw

    self::$api_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    self::$api_header = substr($data, 0, $header_len);

    self::$api_body = (self::$caller == 'SEARCH') ? json_decode(substr($data, $header_len)) : substr($data, $header_len);

    //die(self::$api_body);
  }


  private static function validate_response () {
    // test conditions
    if(self::$api_code == 301 || self::$api_code == 302) {
      self::$debug_message[] = "301 or 302 response from API";
      return false;
    } elseif (empty(self::$api_body)) {
      self::$debug_message[] = "empty body response from API";
      return false;
    } else {
      // if 404
      if( strpos( strtolower(self::$api_header), "404 not found" ) !== false) {
        self::$debug_message[] = "404 response from API";
        return false;
      }
    }

    self::$output->success = true;
    return true;
  }

  // singleton
  public static function getInstance() {
    if (self::$instance == null)
    {
      self::$instance = new HawkSearch();
    }
    return self::$instance;
  }

}

// hardcoded model since its faster than magic methods : https://stackoverflow.com/questions/6184337/best-practice-php-magic-methods-set-and-get
class ApiModel {
  public $Title;
  public $TopText;
  public $BreadCrumb;
  public $TopPager;
  public $BottomPager;
  public $Results;
  public $Merchandising;
  public $Items;
  public $Selections;
  public $Facets;
  public $Related;
  public $FeaturedItems;
  public $Location;
  public $DidYouMean;
  public $DidYouMeanArr;
  public $TrackingId;
  public $MetaRobots;
  public $HeaderTitle;
  public $MetaDescription;
  public $MetaKeywords;
  public $RelCanonical;
  public $Keyword;

  public function set ($data) {
    if (isset($data->Success) && $data->Success) {
      // html route
      $this->Title = $data->Html->Title;
      $this->TopText = $data->Html->TopText;
      $this->BreadCrumb = $data->Html->BreadCrumb;
      $this->TopPager = $data->Html->TopPager;
      $this->BottomPager = $data->Html->BottomPager;
      $this->Results = $data->Html->Results;
      $this->Merchandising = $data->Html->Merchandising;
      $this->Selections = $data->Html->Selections;
      $this->Facets = $data->Html->Facets;
      $this->Related = $data->Html->Related;
      $this->FeaturedItems = $data->Html->FeaturedItems;
      $this->Location = $data->Location;
      $this->DidYouMean = $data->DidYouMean;
      $this->TrackingId = $data->TrackingId;
      $this->MetaRobots = $data->MetaRobots;
      $this->HeaderTitle = $data->HeaderTitle;
      $this->MetaDescription = $data->MetaDescription;
      $this->MetaKeywords = $data->MetaKeywords;
      $this->MetaDescription = $data->MetaDescription;
      $this->RelCanonical = $data->RelCanonical;
      $this->Keyword = $data->Keyword;

      if (!empty($this->DidYouMean)) {
        $this->DidYouMeanArr = explode(",",$this->DidYouMean);
      }
    }
  }
}
