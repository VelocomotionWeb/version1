<?php
/**
 * etranslation traduction agence  
 * 
 * @author    contact@dream-it.ma>
 * @copyright 2015, Dream-it
 * @license   http://www.opensource.org/licenses/MIT
*/

class ETBusiness
{

    public function f_doExport($export_id_lang, $product_rows, $cms_rows, $categ_rows, &$p_filename)
    {
        $UPLOAD_DIR=dirname(__FILE__)."/../uploads/export";
  // products:
  
  // if empty we take all products
        if(empty($product_rows)) {
            $all_products=Product::getProducts($export_id_lang, 1, 10000, 'id_product', 'ASC');
        } else {
            // taking only the requested products
            $all_products=array();
            foreach ($product_rows as $product_row) {
                $l_productObj=new Product($product_row['id_product'], false, $export_id_lang);
                if($l_productObj==false) {
                    $p_message.='unable to load product n°'.$product_row['id_product'];
                    $this->f_log($p_message);
                    continue;
                }
                $all_products[]=array_merge((array)$l_productObj, array('id_product'=>$product_row['id_product']));
            }
        }
  

        $etranslation = new SimpleXMLExtended("<?xml version=\"1.0\" encoding=\"utf-8\" ?><etranslation></etranslation>");//
        $etranslation->addChild('products');
        foreach ($all_products as $product) {
            $product_xml=$etranslation->products->addChild('product');
            $product_xml->addAttribute('id_product', $product['id_product']);
            $product_xml->addChildCData('description', $product['description']);
            $product_xml->addChildCData('description_short', $product['description_short']);
            $product_xml->addChild('name', $product['name']);
            $product_xml->addChild('meta_description', $product['meta_description']);
            $product_xml->addChild('meta_keywords', $product['meta_keywords']);
            $product_xml->addChild('meta_title', $product['meta_title']);
        }

  // CMS
        if(empty($cms_rows)) { // we take all CMS
            $all_cmspages=CMS::getCMSPages($export_id_lang);
        } else { // Taking only the requested CMS pages
            $all_cmspages=array();
            foreach ($cms_rows as $cms_row) {
                $l_cmsObj=new CMS($cms_row['id_cms']);
                $all_cmspages[]=array(
                'id_cms'=>$cms_row['id_cms'],
                'content'=>$l_cmsObj->content[$export_id_lang],
                'meta_description'=>$l_cmsObj->meta_description[$export_id_lang],
                'meta_keywords'=>$l_cmsObj->meta_keywords[$export_id_lang],
                'meta_title'=>$l_cmsObj->meta_title[$export_id_lang]
                );
            }
        }

  

        $etranslation->addChild('cms_pages');
        foreach ($all_cmspages as $cmspage) {
            $cms_xml=$etranslation->cms_pages->addChild('cms_page');
            $cms_xml->addAttribute('id_cms', $cmspage['id_cms']);
            $cms_xml->addChildCData('content', $cmspage['content']);
            $cms_xml->addChild('meta_description', $cmspage['meta_description']);
            $cms_xml->addChild('meta_keywords', $cmspage['meta_keywords']);
            $cms_xml->addChild('meta_title', $cmspage['meta_title']);
        }


  // categories
        $sqlWhere='';
        if(!empty($categ_rows)) { // takes only the requested Categories
        	$sqlParams = array();
        	foreach ($categ_rows as $categ_row) {
//             	array_push($sqlParams,pSQL($categ_row['id_category']));
        		array_push($sqlParams, (int)$categ_row['id_category']);
        	}
//         	$sqlParams = array_map('intval', $sqlParams);
//          $sqlWhere='c.id_category IN ('.implode(',', $sqlParams).') ';
            $sqlWhere='c.id_category IN ('.implode(',', array_map('intval', $sqlParams)).') ';
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
   SELECT c.id_category, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
   FROM `'._DB_PREFIX_.'category` c
   LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.id_lang = '. (int)$export_id_lang.')
   WHERE '.$sqlWhere.'
   GROUP BY c.`id_category`
   ORDER BY `level_depth` ASC
  ');
        $etranslation->addChild('categories');
        foreach ($result as $categ_row) {
            $categ_xml=$etranslation->categories->addChild('category');
            $categ_xml->addAttribute('id_category', $categ_row['id_category']);
            $categ_xml->addChildCData('description', $categ_row['description']);
            $categ_xml->addChild('name', $categ_row['name']);
            $categ_xml->addChild('meta_description', $categ_row['meta_description']);
            $categ_xml->addChild('meta_keywords', $categ_row['meta_keywords']);
            $categ_xml->addChild('meta_title', $categ_row['meta_title']);
        }

  
        $dom = dom_import_simplexml($etranslation)->ownerDocument;
        $dom->formatOutput = true;
        $p_filename="etranslation_".Language::getIsoById($export_id_lang).'_'.date("Ymd_His").'.xml';
        $dom->save($UPLOAD_DIR.'/'.$p_filename);
        return true;
    }
 
 

    public function f_doExporte($export_id_lang, $product_rows, $cms_rows, $categ_rows, &$p_filename, $extcatid, $extelemid, $coltotrid, $date_first, $date_second)
    {
        $UPLOAD_DIR=dirname(__FILE__)."/../uploads/export";
  
        $etranslation = new SimpleXMLExtended("<?xml version=\"1.0\" encoding=\"utf-8\" ?><etranslation></etranslation>");//
  // products:
        if ($extelemid == 0 || $extelemid == 1) {
            if($product_rows=='all')
    { // Taking all products
                $all_products=Product::getProducts($export_id_lang, 0, 1000000, 'id_product', 'ASC');
            } elseif (empty($product_rows))
    {
                $p_message.='No products found !';
                $this->f_log($p_message);
            } else { // takes only the requested products
                $all_products=array();
                foreach ($product_rows as $product_row) {
                    $l_productObj=new Product($product_row['id_product'], false, $export_id_lang);
                    if($l_productObj==false)
                    {
                        $p_message.='unale to load product n°'.$product_row['id_product'];
                        $this->f_log($p_message);
                        continue;
                    }
     
                    $all_products[]=array_merge((array)$l_productObj, array('id_product'=>$product_row['id_product']));
                }
            }
        }
  
   

   

        $etranslation->addChild('products');
		if($all_products!="" && $all_products!=null){
        foreach ($all_products as $product) {
            $product_xml=$etranslation->products->addChild('product');
            $product_xml->addAttribute('id_product', $product['id_product']);
    //$product_xml->addChild('description', $product['description']);
			if( $coltotrid != "")
			{
				foreach ($coltotrid as $column)
				{
					if($column == "description")
					{
						 $product_xml->addChildCData($column, $product[$column]);
					} else {
						$product_xml->addChild($column, $product[$column]);
					}
				}
			}
        }
		}
  

  // CMS

        if ($extelemid == 0 || $extelemid == 3) {
            if($cms_rows == 'all') { // on prend tous les CMS
                $all_cmspages=CMS::getCMSPages($export_id_lang);
            } elseif (empty($cms_rows)) {
                $p_message.='No CMS found !';
                $this->f_log($p_message);
            } else { 
                $all_cmspages=array();
                foreach ($cms_rows as $cms_row) {
                    $l_cmsObj=new CMS($cms_row['id_cms']);

                    $all_cmspages[]=array(
                    'id_cms'=>$cms_row['id_cms'],
                    'content'=>$l_cmsObj->content[$export_id_lang],
                    'meta_description'=>$l_cmsObj->meta_description[$export_id_lang],
                    'meta_keywords'=>$l_cmsObj->meta_keywords[$export_id_lang],
                    'meta_title'=>$l_cmsObj->meta_title[$export_id_lang]
                    );
                }
            }

            $etranslation->addChild('cms_pages');
            foreach ($all_cmspages as $cmspage) {
                $cms_xml=$etranslation->cms_pages->addChild('cms_page');
                $cms_xml->addAttribute('id_cms', $cmspage['id_cms']);
				if( $coltotrid != ""){
					foreach ($coltotrid as $column)
					{
						if($column == "description")
						{
							$cms_xml->addChildCData('content', $cmspage['content']);
						} elseif ($column == "name")
						{
						} else {
							$cms_xml->addChild($column, $cmspage[$column]);
						}
					}
				}
            }
        }

  // categories
        if ($extelemid == 0 || $extelemid == 2) {
            if($categ_rows == 'all') {// Taking all categories
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT c.id_category, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
    FROM `'._DB_PREFIX_.'category` c
    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.id_lang = '. (int)$export_id_lang.')
    GROUP BY c.`id_category`
    ORDER BY `level_depth` ASC
   ');
    
            } elseif (empty($categ_rows)) {
                $p_message.='No categorie found !';
                $this->f_log($p_message);
            } else { 
            	$sqlParams = array();
            	foreach ($categ_rows as $categ_row) {
            		 
//             		array_push($sqlParams,pSQL($categ_row['id_category']));
            		array_push($sqlParams, (int)$categ_row['id_category']);
            	}
//             	$sqlParams = array_map('intval', $sqlParams);
//              $sqlWhere='c.id_category IN ('.implode(',', $sqlParams).') ';
                $sqlWhere='c.id_category IN ('.implode(',', array_map('intval', $sqlParams)).') ';
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT c.id_category, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
    FROM `'._DB_PREFIX_.'category` c
    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.id_lang = '. (int)$export_id_lang.')
    WHERE '.$sqlWhere.'
    GROUP BY c.`id_category`
    ORDER BY `level_depth` ASC
   ');
    
            }
            $etranslation->addChild('categories');
            foreach ($result as $categ_row) {
                $categ_xml=$etranslation->categories->addChild('category');
                $categ_xml->addAttribute('id_category', $categ_row['id_category']);
				if($coltotrid != ""){
					foreach ($coltotrid as $column)
					{
						if($column == "description")
						{
							$categ_xml->addChildCData($column, $categ_row[$column]);
						} else {
							$categ_xml->addChild($column, $categ_row[$column]);
						}
					}
				}
            }
        }

// Groupe d'attribue
        if ($extelemid == 0 || $extelemid == 5) {
   
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT c.id_attribute_group , c.name, c.public_name
    FROM `'._DB_PREFIX_.'attribute_group_lang` c
    WHERE c.id_lang = '. (int)$export_id_lang.'
   ');
    
     
   
   
            $etranslation->addChild('Attribute_Groups');
            foreach ($result as $G_attribute_row) {
                $G_attribute_xml=$etranslation->Attribute_Groups->addChild('Attribute_Group');
                $G_attribute_xml->addAttribute('id_attribute_group', $G_attribute_row['id_attribute_group']);
                $G_attribute_xml->addChild('name', $G_attribute_row['name']);
                $G_attribute_xml->addChild('public_name', $G_attribute_row['public_name']);
    
            }
        
// Attribues
        

   
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT c.id_attribute , c.name
    FROM `'._DB_PREFIX_.'attribute_lang` c
    WHERE c.id_lang = '. (int)$export_id_lang.'
   ');
    
     
   
   
            $etranslation->addChild('Attributes');
            foreach ($result as $attribute_row) {
                $attribute_xml=$etranslation->Attributes->addChild('Attribute');
                $attribute_xml->addAttribute('id_attribute', $attribute_row['id_attribute']);
                $attribute_xml->addChild('name', $attribute_row['name']);
    
            }
        }

// Characteristics
        if ($extelemid == 0 || $extelemid == 6) {
  
   
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT c.id_feature  , c.name
    FROM `'._DB_PREFIX_.'feature_lang` c
    WHERE c.id_lang = '. (int)$export_id_lang.'
   ');
    
     
   
   
            $etranslation->addChild('Features');
            foreach ($result as $feature_row) {
                $feature_xml=$etranslation->Features->addChild('Feature');
                $feature_xml->addAttribute('id_feature', $feature_row['id_feature']);
                $feature_xml->addChild('name', $feature_row['name']);
    
            }
         
// Features Value

   
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
    SELECT c.id_feature_value , c.value
    FROM `'._DB_PREFIX_.'feature_value_lang` c
    WHERE c.id_lang = '. (int)$export_id_lang.'
   ');
    
     
   
   
            $etranslation->addChild('Features_Value');
            foreach ($result as $featureV_row) {
                $featureV_xml=$etranslation->Features_Value->addChild('Feature_Value');
                $featureV_xml->addAttribute('id_feature_value', $featureV_row['id_feature_value']);
                $featureV_xml->addChild('value', $featureV_row['value']); 
            }
        }

 
        $dom = dom_import_simplexml($etranslation)->ownerDocument;
        $dom->formatOutput = true;

        $p_filename="etranslation_".Language::getIsoById($export_id_lang).'_'.date("Ymd_His").'.xml';
  
        $dom->save($UPLOAD_DIR.'/'.$p_filename);


        return true;
    }





###############################################
    public function f_doImport($import_id_lang, &$p_message)
{

        $UPLOAD_DIR=dirname(__FILE__)."/../uploads/import";

        $this->f_log("*******************************");
        $this->f_log("DO_IMPORT, démarrage du process");
        $this->f_log("export_id_lang=".$import_id_lang);

  // Undefined | Multiple Files | $_FILES Corruption Attack
  // If this request falls under any of them, treat it invalid.
        if (!isset($_FILES['file_to_import']['error']) || is_array($_FILES['file_to_import']['error']) ) {
            $p_message='Parametres invalies.';
            $this->f_log($p_message);
            return false;
        }
  // Check $_FILES['upfile']['error'] value.
        switch ($_FILES['file_to_import']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $p_message='No file sent.';
                $this->f_log($p_message);
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $p_message='You have exceeded the maximum size allowed by the server.';
                $this->f_log($p_message);
                return false;
            default:
                $p_message='unknown error.';
                $this->f_log($p_message);
                return false;
        }
  // You should also check filesize here.
        if ($_FILES['file_to_import']['size'] > 20*1024*1024) {
            $p_message='You have exceeded the maximum size allowed (20Mo).';
            $this->f_log($p_message);
            return false;
        }
 
        if(!preg_match("/\.xml$/", $_FILES['file_to_import']['name']))
  {
            $p_message='Your file should have as xml extension('.$_FILES['file_to_import']['name'].')';
            $this->f_log($p_message);
            return false;
        }
        $filename="etranslation_".Language::getIsoById($import_id_lang).'_'.date("Ymd_His").'.xml';

        $this->f_log("moving ".$_FILES['file_to_import']['tmp_name'].' vers '.$filename);

        if (move_uploaded_file($_FILES['file_to_import']['tmp_name'], $UPLOAD_DIR.'/'.$filename)) {
            $this->f_log("moving OK");
        } else {
            $p_message='Error while moving the file to the server';
            $this->f_log($p_message);
            return false;
        }

        libxml_use_internal_errors(true);
        $etranslation = simplexml_load_file($UPLOAD_DIR.'/'.$filename);
        if ($etranslation === false) {
            $p_message="Error loading XML\n";
            foreach (libxml_get_errors() as $error) {
                $p_message.="\t".$error->message;
            }$this->f_log($p_message);
            return false;
        }

        $this->f_log('Product Treatment ');
        foreach ($etranslation->products->product as $product_xml) {
            $id_product=(int)$product_xml['id_product'];
            if(empty($id_product))
   {
                $p_message.='unable to find id_product.';
                $this->f_log($p_message);
                continue;
            }$this->f_log('Treatment of '.$id_product);

            $l_productObj=new Product($id_product, false);
            if($l_productObj==false)
   {
                $p_message.='unable to load product n°'.$id_product;
                $this->f_log($p_message);
                continue;
            }
  
            //Escape $previous_date_upd parameters
            $previous_date_upd=pSQL($l_productObj->date_upd);
            if(!empty($product_xml->description) && ($l_productObj->description[$import_id_lang]!=$product_xml->description)){
                $l_productObj->description[$import_id_lang]=$product_xml->description;
    ;
            }
            if(!empty($product_xml->description_short) && ($l_productObj->description_short[$import_id_lang]!=$product_xml->description_short)){
                $l_productObj->description_short[$import_id_lang]=$product_xml->description_short;
            }
            if(!empty($product_xml->name) && ($l_productObj->name[$import_id_lang]!=$product_xml->name)){
                $l_productObj->name[$import_id_lang]=$product_xml->name;
            }
            if(!empty($product_xml->meta_description) && ($l_productObj->meta_description[$import_id_lang]!=$product_xml->meta_description)){
                $l_productObj->meta_description[$import_id_lang]=$product_xml->meta_description;
            }
            if(!empty($product_xml->meta_keywords) && ($l_productObj->meta_keywords[$import_id_lang]!=$product_xml->meta_keywords)){
                $l_productObj->meta_keywords[$import_id_lang]=$product_xml->meta_keywords;
            }
            if(!empty($product_xml->meta_title) && ($l_productObj->meta_title[$import_id_lang]!=$product_xml->meta_title)){
                $l_productObj->meta_title[$import_id_lang]=$product_xml->meta_title;
            }
   

                        $l_productObj->update();

   // reseting update date of the products
            if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4' ){
                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET date_upd="'.$previous_date_upd.'" WHERE id_product='.(int)$id_product);
            } else {
                Db::getInstance()->update('product', array('date_upd'=>$previous_date_upd), 'id_product='.$id_product, 1);
            }
  



        }

        $this->f_log('cms Page processing');
        foreach ($etranslation->cms_pages->cms_page as $cms_xml) {
            $id_cms=(int)$cms_xml['id_cms'];
            if(empty($id_cms))
   {
                $p_message.='unable to found id_cms.';
                $this->f_log($p_message);
                continue;
            }$this->f_log('Treatment of '.$id_cms);

            $l_cmsObj=new CMS($id_cms);
            if($l_cmsObj==false) {
                $p_message.='unable to load  cms n°'.$id_cms;
                $this->f_log($p_message);
                continue;
            }
  
            if(!empty($cms_xml->content) && ($l_cmsObj->content[$import_id_lang]!=$cms_xml->content)){
                $l_cmsObj->content[$import_id_lang]=$cms_xml->content;
            }
            if(!empty($cms_xml->meta_keywords) && ($l_cmsObj->meta_keywords[$import_id_lang]!=$cms_xml->meta_keywords)){
                $l_cmsObj->meta_keywords[$import_id_lang]=$cms_xml->meta_keywords;
            }
            if(!empty($cms_xml->meta_title) && ($l_cmsObj->meta_title[$import_id_lang]!=$cms_xml->meta_title)){
                $l_cmsObj->meta_title[$import_id_lang]=$cms_xml->meta_title;
            }
            if(!empty($cms_xml->meta_description) && ($l_cmsObj->meta_description[$import_id_lang]!=$cms_xml->meta_description)){
                $l_cmsObj->meta_description[$import_id_lang]=$cms_xml->meta_description;
            }

                $l_cmsObj->update();

        }


        $this->f_log(' categories processing');
        foreach ($etranslation->categories->category as $categ_xml) {
            $id_category=(int)$categ_xml['id_category'];
            if(empty($id_category)) {
                $p_message.='unable to find  id_category.';
                $this->f_log($p_message);
                continue;
            }$this->f_log('Treatment of '.$id_category);

            $l_categObj=new Category($id_category);
            if($l_categObj==false) {
                $p_message.='unable to load catégorie n°'.$id_category;
                $this->f_log($p_message);
                continue;
            }
   
            //Escape $previous_date_upd parameters
            $previous_date_upd=pSQL($l_categObj->date_upd);
   
   
   
   
            if(!empty($categ_xml->description) && ($l_categObj->description[$import_id_lang]!=$categ_xml->description)){
                $l_categObj->description[$import_id_lang]=$categ_xml->description;
            }
     
            if(!empty($categ_xml->name) && ($l_categObj->name[$import_id_lang]!=$categ_xml->name)){
                $l_categObj->name[$import_id_lang]=$categ_xml->name;
            }
            if(!empty($categ_xml->meta_description) && ($l_categObj->meta_description[$import_id_lang]!=$categ_xml->meta_description)){
                $l_categObj->meta_description[$import_id_lang]=$categ_xml->meta_description;
            }
            if(!empty($categ_xml->meta_keywords) && ($l_categObj->meta_keywords[$import_id_lang]!=$categ_xml->meta_keywords)){
                $l_categObj->meta_keywords[$import_id_lang]=$categ_xml->meta_keywords;
            }
            if(!empty($categ_xml->meta_title) && ($l_categObj->meta_title[$import_id_lang]!=$categ_xml->meta_title)){
                $l_categObj->meta_title[$import_id_lang]=$categ_xml->meta_title;
            }
            if(!empty($categ_xml->content) && ($l_categObj->content[$import_id_lang]!=$categ_xml->content)){
                $l_categObj->content[$import_id_lang]=$categ_xml->content;
            }
            $l_categObj->update();

   
            if (Tools::substr(_PS_VERSION_, 0, 3) == '1.4' ) {
                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'category SET date_upd="'.$previous_date_upd.'" WHERE id_category='.(int)$id_category);
            } else {
                Db::getInstance()->update('category', array('date_upd'=>$previous_date_upd), 'id_category='.$id_category, 1);
            }
        }
      
// import attribue group
        $this->f_log('Treatment attribue group');
        foreach ($etranslation->Attribute_Groups->Attribute_Group as $G_attribute_xml) {
            $id_attribute_group=(int)$G_attribute_xml['id_attribute_group'];
            if(empty($id_attribute_group)) {
                $p_message.='unable to find id_attribute_group.';
                $this->f_log($p_message);
                continue;
            }$this->f_log('Treatment of '.$id_attribute_group);
   
            $G_Att_name = $G_attribute_xml->name;
            $G_Att_Pname = $G_attribute_xml->public_name;
   
   
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'attribute_group_lang SET name="'.pSQL($G_Att_name).'", public_name="'.$G_Att_Pname.'" WHERE id_attribute_group='.(int)$id_attribute_group.' AND id_lang='.(int)$import_id_lang);
        }
   
   
   // import Attribues
        $this->f_log('Treatment of attribues');
        foreach ($etranslation->Attributes->Attribute as $attribute_xml) {
            $id_attribute=(int)$attribute_xml['id_attribute'];
            if(empty($id_attribute)) {
                $p_message.='unable to find id_attribute.';
                $this->f_log($p_message);
                continue;
            }$this->f_log('Traitement de '.$id_attribute);
   
            $Att_name = $attribute_xml->name;
   
   
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'attribute_lang SET name="'.pSQL($Att_name).'" WHERE id_attribute='.(int)$id_attribute.' AND id_lang='.(int)$import_id_lang);
        }
         
         
         
         
         
   // Features import
        $this->f_log('Processing Features');
        foreach ($etranslation->Features->Feature as $feature_xml) {
            $id_feature=(int)$feature_xml['id_feature'];
            if(empty($id_feature)) {
                $p_message.='impossible de trouver id_feature.';
                $this->f_log($p_message);
                continue;
            }$this->f_log('Treatment '.$id_feature);
   
            $Feat_name = $feature_xml->name;
   
   
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'feature_lang SET name="'.pSQL($Feat_name).'" WHERE id_feature='.(int)$id_feature.' AND id_lang='.(int)$import_id_lang);
        }
         
// import Features Value
        $this->f_log('Processing Features Value');
        foreach ($etranslation->Features_Value->Feature_Value as $featureV_xml) {
            $id_feature_value=(int)$featureV_xml['id_feature_value'];
            if(empty($id_feature_value)) {
                $p_message.='unable to find id_feature_value.';
                $this->f_log($p_message);
                continue;
            }$this->f_log('Treatment  '.$id_feature_value);
   
            $Feat_value = $featureV_xml->value;
   
   
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'feature_value_lang SET value="'.pSQL($Feat_value).'" WHERE id_feature_value='.(int)$id_feature_value.' AND id_lang='.(int)$import_id_lang);
        }
   
        return true;


    }




    public function f_log($p_msg)
    {
        $l_log_desc=dirname(__FILE__)."/../logs/".date("Y-m-d").".log";
        $p_msg=preg_replace("/[\t\r\n]/", " ", $p_msg);
        $p_msg=date("Y-m-d H:i:s").' : '.$p_msg."\n";
        error_log($p_msg, 3, $l_log_desc);
    }


    public function f_send_file($p_filename)
    {
    	if(1 == preg_match('=^[^/?*;:{}\\\\]+\.[^/?*;:{}\\\\]+$=', $p_filename)){
	        $UPLOAD_DIR=dirname(__FILE__)."/../uploads/export";
	        $file = realpath($UPLOAD_DIR.'/'.$p_filename);
	        if(dirname($file) == realpath($UPLOAD_DIR)){
		        ob_clean();
		        $ze_string = implode('', file($file));
		  
		        header("Content-Type: application/force-download;name=\"" .$p_filename . "\"");
		        header("Content-Transfer-Encoding: binary");
		        header("Content-Length: ".Tools::strlen($ze_string));
		        header("Content-Disposition: attachment;filename=\"" . $p_filename . "\"");
		        header("Expires: 0");
		        header("Cache-Control: no-cache, must-revalidate");
		        header("Pragma: no-cache");
		        readfile ($file);
		        exit;
        	}
    	}
   }

 
    public function f_send_mail($email, $extlgid, $langdest, $p_filename)
    {
        try {
        	if(1 == preg_match('=^[^/?*;:{}\\\\]+\.[^/?*;:{}\\\\]+$=', $p_filename)){
	            $file_name = $p_filename;
	            $path = dirname(__FILE__)."/../uploads/export/";
	
	            $file = realpath($path.$file_name);
	            if(dirname($file) == realpath($path)){
		            $file_size = filesize($file);
		            $handle = fopen($file, "r");
		            $content = fread($handle, $file_size);
		            fclose($handle);
		   
		            $client = new SoapClient(null, array(
		            'location' => "http://e-translation-agency.com/wp-content/prestashopApi/Server.php",
		            'uri' => "http://e-translation-agency.com/wp-content/prestashopApi/Server"));
		     
		            $domaine = $_SERVER['SERVER_NAME'];
		
		            $result = $client->send_mail($email, $domaine, $extlgid, $langdest, $content, $file_name);
		            return $result;
	            }
	        }
        }catch(SoapFault $ex) {
            $ex->getMessage();
        }
    }





    public function f_send_cron($email, $msg, $p_filename)
    {
        try {
        	if(1 == preg_match('=^[^/?*;:{}\\\\]+\.[^/?*;:{}\\\\]+$=', $p_filename)){
	            $file_name = $p_filename;
	            $path = dirname(__FILE__)."/../uploads/export/";
	            $file = realpath($path.$file_name);
	            if(dirname($file) == realpath($path)){
		            $file_size = filesize($file);
		            $handle = fopen($file, "r");
		            $content = fread($handle, $file_size);
		            fclose($handle);
		   
		            $client = new SoapClient(null, array(
		            'location' => "http://e-translation-agency.com/wp-content/prestashopApi/Server.php",
		            'uri' => "http://e-translation-agency.com/wp-content/prestashopApi/Server"));
		          
		            $result = $client->send_cron($email, $msg, $p_filename, $content);
		            return $result;
	            }
        	}
        }catch(SoapFault $ex) {
            $ex->getMessage();
        }
    }
 
    public function f_files_list()
    {
        $dir = dirname(__FILE__)."/../views/templates/admin/server/php/files/";
        $files = [];
        $i = 0;
  
  
        if (is_dir($dir)) {

    
            if ($dh = opendir($dir)) {

      
                while (($file = readdir($dh)) !== false) {
       
                    if($file != '.' && $file != '..' && $file != 'index.php') {
                        if (filetype($dir . $file) != "dir"){
                            $files[$i] = $file;
                            $i++;
                        }
                    }
                }
     
                closedir($dh);
               
            }
        }return $files;

    }
   
    public function f_send_flsmail($liste_fichier)
    {
        try {
     
  
           
            $dir = dirname(__FILE__)."/../views/templates/admin/server/php/files/";
            
            $i=0;
            
            $attachement = '';
            if (sizeof($liste_fichier)>1){
               while($i<sizeof($liste_fichier)) { // iterate files
                    $liste_fichier[$i] = $dir . $liste_fichier[$i];
                    $i++;
               }
            
                $destination = $dir . "thumbnail/eTranslation.zip";
               if ($this->create_zip($liste_fichier, $destination, true) == false){
                    return 0;
               }
            
            } else {
                $destination = $dir . $liste_fichier[0];
            }   

           
            $info = new SplFileInfo($destination);
            $extention = $info->getExtension();
            $fileName =$info->getFilename () ;
            $type ="";
            if($extention == "doc" || $extention == "docx") {
                $type = "application/msword";
            } elseif ($extention == "xls" || $extention == "xlsx") {
                $type = "application/vnd.ms-excel";
            } elseif ($extention == "pdf") {
                $type = "application/octet-stream";
            } elseif ($extention == "xml") {
                $type = "application/xml";
            } elseif ($extention == "csv") {
                $type = "application/csv";
            } elseif ($extention == "zip") {
                $type = "application/zip";
            } elseif ($extention == "jpeg" || $extention == "png" || $extention == "gif" || $extention == "jpg") {
                $type = "image/".$extention;
            } else {
                $type = "application/{$extention}";
            }
            
            $fd = fopen($destination, "rb" );
            $contenu = fread($fd, filesize($destination));

            
            $attachement .= chunk_split(base64_encode($contenu));
            

            fclose($fd);
            
            $domaine = $_SERVER['SERVER_NAME'];
			if (sizeof($liste_fichier)>1){
				foreach ($liste_fichier as $file){ // iterate files
					if(is_file($dir.$file))
					  chmod($dir,0777);
						
						unlink($file);// delete file
						
				}
			}else{
					chmod($dir,0777);
					unlink($dir.$liste_fichier[0]);
				}
            //SOAP Call
            $client = new SoapClient(null, array(
            'location' => "http://e-translation-agency.com/wp-content/prestashopApi/Server.php",
            'uri' => "http://e-translation-agency.com/wp-content/prestashopApi/Server"));
            $result = $client->send_files($domaine, $attachement, $type, $fileName);
            
            return $result;
        }
        catch(SoapFault $ex) {
            $ex->getMessage();
        }

    }
 /* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false) {
   //if the zip file already exists and overwrite is false, return false
   
        if(file_exists($destination) && !$overwrite) { 
      return false;
       }
   
        $valid_files = array();
   //if files were passed in...
        if(is_array($files)) {
      //cycle through each file
            foreach ($files as $file) {
            //make sure the file exists
                if(file_exists($file)) {
               $valid_files[] = $file;
               }
           }
       }
   
   //if we have good files...
        if(count($valid_files)) {
      //create the archive
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
       }
      //add the files
        foreach ($valid_files as $file) {
            $zip->addFile($file,basename($file));
       }
      
      
      //close the zip -- done!
        $zip->close();
      
      //check to make sure the file exists
        return file_exists($destination);
       }
        else
        {
            return false;
       }
}
 
   static function f_test_directory($p_directory_name)
    {
        if(is_writeable(dirname(__FILE__)."/../".$p_directory_name)) {
            return true;
        } else {
            return false;
        }
 }
 
 

}




class SimpleXMLExtended extends SimpleXMLElement
{
 /**
 * Add CDATA text in a node
 * @param string $cdata_text The CDATA value  to add
 */
 private function addCData($cdata_text)
 {
        $node= dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
 }

 /**
 * Create a child with CDATA value
 * @param string $name The name of the child element to add.
 * @param string $cdata_text The CDATA value of the child element.
 */
    public function addChildCData($name,$cdata_text)
 {
        $child = $this->addChild($name);
        $child->addCData($cdata_text);
    }
    
   
    

}