<?php

// hardcoded model since its after than magic methods : https://stackoverflow.com/questions/6184337/best-practice-php-magic-methods-set-and-get
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
      // proxy/hybrid route
      $this->Title = $data->Data->Title;
      $this->TopText = $data->Data->TopText;
      $this->BreadCrumb = $data->Data->BreadCrumb;
      $this->TopPager = $data->Data->TopPager;
      $this->BottomPager = $data->Data->BottomPager;
      $this->Results = $data->Data->Results;
      $this->Merchandising = $data->Data->Merchandising;
      $this->Selections = $data->Data->Selections;
      $this->Facets = $data->Data->Facets;
      $this->Related = $data->Data->Related;
      $this->FeaturedItems = $data->Data->FeaturedItems;
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
