<?php
include_once('../config.php');
include_once('model.php');
include_once('search.php');
include_once('render.php');

$params = [];
parse_str($_SERVER['QUERY_STRING'], $params);
$a = HawkSearch::search($params);
$render = new Render($a);
$r = $render->results(); // render onload

// print_r($r); die();

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
          HawkSearch.BaseUrl = '<?php echo HYBRID_PROXY_URL ?>';
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

<form method="get" action="/hybridproxy/" style="margin-bottom: 25px; background-color: #0f0f0f; padding: 10px;">
  <input type="text" id="searchbox" class="" name="keyword" placeholder="<?php echo (!empty($_GET["keyword"])) ? htmlspecialchars($_GET["keyword"], ENT_QUOTES, 'UTF-8') : ''; ?>">
  <input type="submit">
</form>

<div class="row container hawksearch"> <!-- hawksearch -->

      <?php if (!isset($a->params->keyword) || empty($a->params->keyword)): ?>
        <!-- ## BLANK KEYWORD ## -->

      <?php elseif(!isset($a->api)): ?>
        <!-- ## ISSUES ## -->
        <div class="grid_3"></div>
        <div class="grid_9">
          Sorry we are having issues with our search.  We are actively working on it.
        </div>
      <?php elseif ($a->no_results): ?>
        <!-- ## OTHER RESULTS ## -->
        <div class="grid_3">&nbsp;</div>
        <div class="grid_9">
          <?php if ($a->success): ?>
            <?php $items = (isset($a->api->Results)) ? json_decode($a->api->Results) : null; ?>
              <?php if(isset($a->api->Merchandising->Items[0]->Html)): ?>
                <div id="noresultssuggestions">
                  <h3>Sorry, can't find what you're looking for.</h3>
                  <p>How about you try:</p>
                  <?php echo $a->api->Merchandising->Items[0]->Html; ?>
                </div>
              <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <!-- ## RESULTS ## -->
        <?php echo $r ?>
      <?php endif; ?>

    </div> <!-- hawksearch -->

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
