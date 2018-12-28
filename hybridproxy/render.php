<?php
class Render {
  private $_api_results;

  private $_products = [];
  private $_debug_results = false;
  private $_debug_work = false;
  private $_page = "";

  private $_tracking_id;

  function __construct($api_results)
  {
    // debugs
    if (isset($api_results->params->debug) && $api_results->params->debug == 'api') {
      print_r ($api_results); die();
    }

    // assign
    $this->_api_results = $api_results;
    $this->_tracking_id = (isset($this->_api_results->api->TrackingId) && !empty($this->_api_results->api->TrackingId))? $this->_api_results->api->TrackingId : null;

    // build!
    $this->build();
  }

  public function results() {
    return $this->_page;
  }

  public function proxy_results() {
    $obj = new StdClass();
    $obj->Success = $this->_api_results->success; // why is this capitalized?
    $obj->html = $this->_page;
    $obj->location = $this->_api_results->api->Location;
    $obj_json = json_encode($obj);

    $r = $this->_api_results->params->callback . "(" . $obj_json . ")";
    return $r;
  }

  private function build () {
    // print_r($this->_api_results); die();
    if (!$this->_api_results->no_results) {
      // get results set
      try {
        $items = json_decode($this->_api_results->api->Results);
      } catch (Exception $e) {
        $items = null;
      }

      ob_start();
      ?>
      <!-- side menu -->
      <div class="grid_3">
        <div id="hawkbannerlefttop"><!-- Area for Banner above filters on Top --> </div>
        <div id="hawkfacets"><?=$this->_api_results->api->Facets?></div>
        <div id="hawkbannerleftbottom"><!-- Area for Banner below filters --></div>
      </div>

      <!-- results -->
      <div class="grid_9">
        <div id="hawktitle"><!-- <?=$this->_api_results->api->Title?> --></div>
        <div id="hawkbreadcrumb"><!-- <?=$this->_api_results->api->BreadCrumb?> --></div>
        <div id="hawkbannertop"></div>

        <?php if (isset($this->_api_results->api->DidYouMeanArr)): ?>
          <div id="didyoumean">
            <p><span class="alert">Did you mean?</span></p>
            <p>
              <?php foreach ($this->_api_results->api->DidYouMeanArr as $m): ?>
                <a href="/search?keyword=<?=$m?>"><?=$m?></a>&nbsp;
              <?php endforeach; ?>
            </p>
            <br />
            <p><span class="alert">Showing results for <strong><?=$this->_api_results->api->Keyword; ?></strong> instead of </span> <strong><?= $this->_api_results->params->keyword; ?></strong>.</p>
          </div>
        <?php endif; ?>

        <div id="hawktoptext"><!-- <?=$this->_api_results->api->TopText?> --></div>
        <div id="hawktoppager"><?=$this->_api_results->api->TopPager?></div>
        <div id="hawkitemlist">
          <div id="products-list" class="product-results">
            <?php
              if (!empty($items)){
                $c = 0;
                foreach($items->Items as $i) {
                  $c++;
                  if ($this->_api_results->api_type == 'content') {
                    $this->build_content($i,$c);
                  } else {
                    $this->build_item($i,$c);
                  }
                }
              }
            ?>
          </div>
        </div>
        <div id="hawkbannerbottom"></div>
        <div id="hawkbannerbottom2"></div>
        <div id="hawkbottompager"><?=$this->_api_results->api->BottomPager?></div>
      </div>

      <?php
      $this->_page = ob_get_clean();
      ob_flush();
    }
  }

  private function build_item ($i, $c) {
?>
    <article>
      <div class="small-12 medium-12 large-12 columns">
        <a class="search__imglink" href="<?=$default->CustomUrl?>" onclick="return HawkSearch.link(event,'<?=$this->_tracking_id?>',<?=$c?>,'<?=$i->Id?>',0);">
          <h3><?= $i->ItemName?></h3>
        </a>
        <span style="font-size: 12px;"><? print_r($i); ?></span>
      </div>
    </article>
    <br clear="all">
    <hr />
<?php
  }

  private function build_content ($i, $c) {
?>
    <div class="item hawk-contentItem ">
      <div class="content hawk-contentWrapper">
        <h3 class="title hawk-contentTitle">
          <?php if (empty($this->_tracking_id)): ?>
            <a href="<?=$i->CustomURL?>"><?=$i->ItemName?></a>
          <?php else: ?>
            <a href="<?=$i->CustomURL?>" onclick="return HawkSearch.link(this,'<?=$this->_tracking_id?>',<?=$c?>,'<?=$i->Id?>',0);"><?=$i->ItemName?></a>
          <?php endif; ?>
        </h3>
        <p class="hawk-contentCaption">
          <?= (strlen($i->Custom->description_short) > 258) ? substr($i->Custom->description_short,0,255).'<span class="search-hellip">&#8230;</span>' : $i->Custom->description_short ?>
        </p>
      </div>
    </div>

<?php
  }
}
