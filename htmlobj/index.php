<?php
include_once('../config.php');
include_once('search.php');

$params = [];
parse_str($_SERVER['QUERY_STRING'], $params);
$a = HawkSearch::search($params);

//print_r($a); die();

?><html>
<head>
  <script src="https://code.jquery.com/jquery-1.11.0.min.js" type="text/javascript"></script>
  <script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js" type="text/javascript"></script>
  <link rel="stylesheet" type="text/css" href="../styles.css">

  <!-- Hawk Search Header Includes -->
  <script type="text/javascript">
      //<![CDATA[
      (function (HawkSearch, undefined) {
          HawkSearch.BaseUrl = '<?php echo HTML_PROXY_URL ?>';
          HawkSearch.TrackingUrl =
              'http://dev.hawksearch.net/sites/<?php echo ENGINE_NAME ?>';
          if ("https:" == document.location.protocol) {
              HawkSearch.BaseUrl = HawkSearch.BaseUrl.replace("http://",
                  "https://");
              HawkSearch.TrackingUrl =
                  HawkSearch.TrackingUrl.replace("http://", "https://");
          }
      }(window.HawkSearch = window.HawkSearch || {}));
      var hawkJSScriptDoc = document.createElement("script");
      hawkJSScriptDoc.async = true;
      hawkJSScriptDoc.src = HawkSearch.TrackingUrl +
          '/includes/hawksearch.js?v1.0';
      var hawkJSTag = document.getElementsByTagName('script')[0];
      hawkJSTag.parentNode.insertBefore(hawkJSScriptDoc, hawkJSTag);
      var hawkCSSScriptDoc = document.createElement("link");
      hawkCSSScriptDoc.setAttribute("rel", "stylesheet");
      hawkCSSScriptDoc.setAttribute("type", "text/css");
      hawkCSSScriptDoc.setAttribute("href", HawkSearch.TrackingUrl +
          '/includes/hawksearch.css');
      document.head.appendChild(hawkCSSScriptDoc);
      //]]>
  </script>
</head>
<body>

<form method="get" action="/htmlobj/" style="margin-bottom: 25px; background-color: #0f0f0f; padding: 10px;">
  <input type="text" id="searchbox" class="" name="keyword" placeholder="<?php echo (!empty($_GET["keyword"])) ? htmlspecialchars($_GET["keyword"], ENT_QUOTES, 'UTF-8') : ''; ?>">
  <input type="submit">
</form>

<?php if (empty($a->params)): ?>
  <div class="not-found">
    <h1>Search!</h1>
    <form method="get" action="/htmlobj/" style="margin-bottom: 25px;">
      <label>Search:</label>
      <input type="text" id="textboxname" class="textboxname" name="keyword" placeholder="<?php echo (!empty($_GET["keyword"])) ? htmlspecialchars($_GET["keyword"], ENT_QUOTES, 'UTF-8') : ''; ?>">
      <input type="submit">
    </form>
  </div>
<?php else: ?>
  <div class="container">
    <div class="grid_3">
      <div class="hawk-facetScollingContainer">
        <div id="hawkbannerlefttop"><!-- Area for Banner above filters on Top --> </div>
        <div id="hawkfacets"><?=$a->api->Facets?></div>
        <div id="hawkbannerleftbottom"><!-- Area for Banner below filters --></div>
      </div>
    </div>

    <div class="grid_9">
      <form method="get" action="/htmlobj/" style="margin-bottom: 25px;">
        <label>Search:</label>
        <input type="text" id="textboxname" class="textboxname" name="keyword" placeholder="<?php echo (!empty($keyword)) ? $keyword : ''; ?>">
        <input type="submit">
      </form>

      <?php if (!empty($a->api->TopText)): ?>
        <?=$a->api->TopText?>
      <?php endif ?>

      <div id="hawktitle"><!-- <?=$a->api->Title?> --></div>
      <div id="hawkbreadcrumb"><!-- <?=$a->api->BreadCrumb?> --></div>
      <div id="hawkbannertop"></div>
      <div id="hawktoptext"><!-- <?=$a->api->TopText?> --></div>
      <div id="hawktoppager"><?=$a->api->TopPager?></div>

      <div id="hawkitemlist">
        <?php echo $a->api->Results; ?>
      </div>

      <div id="hawkbannerbottom"></div>
      <div id="hawkbannerbottom2"></div>
      <div id="hawkbottompager"><?=$a->api->BottomPager?></div>
    </div>
  </div>
<?php endif; ?>


<hr>
<h1>Debug Data:</h1>
<ul>
  <li><b>success:</b> <?=$a->success?></li>
  <li><b>proxy url:</b> <?=$a->proxy_url?></li>
  <li><b>api uri: </b> <?=$a->api_uri?></li>
  <li><b>enginename:</b> <?=$a->engine_name?></li>
  <li><b>env:</b> <?=$a->environment?></li>
  <li><b>resp:</b> <?php //var_dump((strip_tags($m->api))) ?></li>
  <?php if($a->success):?>
    <li><b>Location:</b> <?=$a->api->Location?></li>
    <li><b>DidYouMean:</b> <?=$a->api->DidYouMean?></li>
    <li><b>TrackingId:</b> <?=$a->api->TrackingId?></li>
    <li><b>Keyword:</b> <?=$a->api->Keyword?></li>
    <li><b>Merchandising:</b> <?php print_r($a->api->Merchandising);?></li>
    <li><b>Related:</b> <?=$a->api->Related?></li>
    <li><b>Selections:</b> <?=$a->api->Selections?></li>
    <li><b>FeaturedItems:</b> <?php print_r($a->api->FeaturedItems);?></li>
  <?php endif;?>
</ul>

<!-- Hawk Search - Auto-Suggest -->
<script type="text/javascript">
    //<![CDATA[
    HawkSearch.initAutoSuggest = function () {
        HawkSearch.suggestInit('#textboxname', {
            lookupUrlPrefix: HawkSearch.TrackingUrl + '/?fn=ajax&f=GetSuggestions',
            hiddenDivName: '',
            isAutoWidth: true
        });
        HawkSearch.suggestInit('#searchbox', {
            lookupUrlPrefix: HawkSearch.TrackingUrl + '/?fn=ajax&f=GetSuggestions',
            hiddenDivName: '',
            isAbove: false
        });
    };
    //]]>
</script>
</body>
<footer class="footer"></footer>
</html>
