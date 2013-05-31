<?php
class Article_Headline_Toggle extends Plugin {

  private $host;


  function about() {
    return Array(
        1.2 // version
      , "Toggle article visibility by clicking on the headline" // description
      , "wn" // author
      , false // is system
      , "https://www.github.com/supahgreg/ttrss-article-headline-toggle" // more info URL
    );
  }


  function api_version() {
    return 2;
  }


  function init($host) {
    $this->host = $host;

    //$host->add_hook($host::HOOK_PREFS_TAB, $this);
    //$host->add_hook($host::HOOK_PREFS_TAB_SECTION, $this);
  }
  

  /**
   * Give a hint when hovering over an article's headline.
   */
  function get_css() {
    return "#headlines-frame > div > div.cdmHeader > span.titleWrap {"
         . " cursor: pointer;"
         . "}"
         . "div.cdm.expandable.active div.cdmHeader a.title {"
         . "color: black;"
	       . "font-weight: bold;"
         . "}"
         . "span.collapseBtn {"
         . "display: none;"
	       . "}"
         ;
  }


  /**
   * Wrapping the cdmClicked function to toggle an article
   * when its headline is clicked.
   *
   * TODO: Add a pref to control whether pre-expanded articles
   * (from "Automatically expand articles in combined mode")
   * are eligible to be toggled.
   */
  function get_js() {
    return <<<'JS'
;(function(cdmClicked) {
  // Do nothing if the user is forcing the expanded view
  if (getInitParam("cdm_expanded")) return;

  var oldClicked = cdmClicked;

  function _cdmClicked(aEvent, aId) {
    var titleId = "RTITLE-" + aId
      , wasActive = $("RROW-" + aId).hasClassName("active")
      , ret = oldClicked.call(null, aEvent, aId)
      ;

    var classNameMatch = ["title", "titleWrap", "cdmExcerpt"];
    if (!aEvent.ctrlKey && classNameMatch.indexOf(aEvent.target.className) !== -1) {
      if (wasActive)
        cdmCollapseArticle(null, aId);
      else
        cdmExpandArticle(aId);
    } else {
        return ret;
    }

    return false;
  }

  window.cdmClicked = _cdmClicked;
})(cdmClicked);
JS;
  }
}
?>
