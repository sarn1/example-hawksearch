<?php

// hardcoded model since its after than magic methods : https://stackoverflow.com/questions/6184337/best-practice-php-magic-methods-set-and-get
class WorkModel {
  public $WorkId; // need
  public $ProductForms = [];
  public $Scrollable;

  function __construct($i, $c) {
    $this->WorkId = $c;

    $items = rand(1,10);

    $this->Scrollable = ($items > 8) ? true : false;

    // todo
    for ($t = 1; $t <= $items; $t++) {
      $prod = new ProductModel($i, $t);
      $this->ProductForms[$prod->Isbn] = $prod;
    }
  }
}


class ProductModel {
  public $Title;
  public $Isbn;
  public $Price;
  public $PriceSale;
  public $IsOnSale;
  public $Binding;
  public $Author; // author json
  public $ImageUrl;
  public $CustomUrl;
  public $Annotation;
  public $Rand;


  // var
  public $IsDigital;
  public $IsDefault; // need
  public $BindingJson; // which an array of productModel


  function __construct($i, $t)
  {
    $rand = $this->generateRandomString(5);
    $this->Rand = $rand;

    $this->Author = "######".$rand;
    $this->Binding = (isset($i->Custom->binding)) ? $i->Custom->binding.$rand : null;
    $this->CustomUrl = $i->CustomURL.$rand;
    $this->ImageUrl = (empty($i->ImageURL) || stripos($i->ImageURL, '/images/missing.jpg') !== false  || stripos($i->ImageURL, '/shared/images/spacer.gif')  !== false) ? $i->ImageURL : str_replace('height=160&width=160','width=190',$i->ImageURL);
    $this->Isbn = $i->Id.$rand;
    $this->IsOnSale = ($i->IsOnSale == 1) ? true : false;
    $this->PriceSale = ($i->IsOnSale == 1) ? $this->format_money($i->SalePrice) : null;
    $this->Price = $this->format_money($i->Price);
    $this->Title = $i->ItemName.$rand;
    $this->Annotation = isset($i->Custom->description_long) && !empty(($i->Custom->description_long)) ? \Tyndale\Format::neat_trim( $i->Custom->description_long, 255, '') : null;
    $this->IsDefault = ($t==1) ? true : false;
    $this->IsDigital = (strpos($this->Binding, 'ebook') !== false || stripos($this->Binding, 'download') !== false )? true : false;
  }

  private function format_money ($m) {
    if (!empty($m)) {
      $m = str_replace("+","",$m);
      $m = ltrim($m,"0");
      $m = number_format((float)$m, 2, '.', '');
      return $m;
    }
    return "ERROR";
  }

  private function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return '-'.$randomString;
  }
}


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
  //public $Original;

  public function set ($data) {
    if (isset($data->Success) && $data->Success) {
      // hybrid proxy route
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
