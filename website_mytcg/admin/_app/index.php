<?php
require_once "../../func.php";

$sCRLF="\r\n";
$sTab=chr(9);


//imageservers save
if (intval($_GET['imageserversave'])==1){
	$iID=intval($_GET['id']);
	$sDescription=$_GET['description'];
	$aSave=myqu(
		'UPDATE '
		.$Conf["database"]["table_prefix"].'_imageserver '
		.'SET '
		.'description="'.$sDescription.'" '
		.'WHERE imageserver_id="'.$iID.'"'
	);
	$_GET['imageservers']=1;
}


//imageservers page
if (intval($_GET['imageservers'])==1){
	$aImageServers=myqu(
		'SELECT imageserver_id, description '
		.'FROM '.$Conf["database"]["table_prefix"].'_imageserver '
		.'ORDER BY description'
	);
	$iSize=sizeof($aImageServers);
	echo '<imageservers>'.$sCRLF;
	for ($iCount=0;$iCount<$iSize;$iCount++){
		echo $sTab.'<imageserver_'.$iCount.'>'.$sCRLF;
		echo $sTab.$sTab.'<imageserver_id val="'
			.$aImageServers[$iCount]['imageserver_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<description val="'
			.$aImageServers[$iCount]['description'].'" />'.$sCRLF;
		echo $sTab.'</imageserver_'.$iCount.'>'.$sCRLF;
	}
	echo '</imageservers>'.$sCRLF;
}






//user new
if (intval($_GET['usernew'])==1){
	$sUsername=$_GET['username'];
	$sEmailAddress=$_GET['emailaddress'];
	$sRealName=$_GET['realname'];
	$iCredits=intval($_GET['credits']);
	$iIsActive=intval($_GET['isactive']);
	$aSave=myqu(
		'INSERT INTO '
		.$Conf["database"]["table_prefix"].'_user '
		.'(username, email_address, name, credits, is_active) '
		.'VALUES ("'.$sUsername.'", '
		.'"'.$sEmailAddress.'", '
		.'"'.$sRealName.'", '
		.'"'.$iCredits.'", '
		.'"'.$iIsActive.'")'
	);
	$_GET['users']=1;
}


//users page
if (intval($_GET['users'])==1){
	$aUsers=myqu(
		'SELECT user_id, username, name, email_address, is_active, '
		.'date_register, date_last_visit, credits '
		.'FROM '.$Conf["database"]["table_prefix"].'_user '
		.'WHERE is_deleted IS NULL '
		.'ORDER BY username'
	);
	echo '<users>'.$sCRLF;
	$iSize=sizeof($aUsers);
	for ($iCount=0;$iCount<$iSize;$iCount++){
		echo $sTab.'<user_'.$iCount.'>'.$sCRLF;
		echo $sTab.$sTab.'<user_id val="'
			.$aUsers[$iCount]['user_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<username val="'
			.$aUsers[$iCount]['username'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<email_address val="'
			.$aUsers[$iCount]['email_address'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<name val="'
			.$aUsers[$iCount]['name'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<credits val="'
			.$aUsers[$iCount]['credits'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<is_active val="'
			.$aUsers[$iCount]['is_active'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<date_register val="'
			.$aUsers[$iCount]['date_register'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<date_last_visit val="'
			.$aUsers[$iCount]['date_last_visit'].'" />'.$sCRLF;
		echo $sTab.'</user_'.$iCount.'>'.$sCRLF;
	}
	echo '</users>'.$sCRLF;
}

//product delete
if (intval($_GET['productdelete'])==1){
	$iID=$_GET['id'];
	$aSave=myqu(
		'UPDATE '
		.$Conf["database"]["table_prefix"].'_product '
		.'SET '
		.'is_deleted="1" '
		.'WHERE product_id="'.$iID.'"'
	);
	$_GET['products']=1;
}


//product new
if (intval($_GET['productnew'])==1){
	$sDescription=$_GET['description'];
	$iPrice=intval($_GET['price']);
	$iInStock=intval($_GET['instock']);
	$sImage=$_GET['image'];
	$iIsActive=intval($_GET['isactive']);
	$aSave=myqu(
		'INSERT INTO '
		.$Conf["database"]["table_prefix"].'_product '
		.'(description, price, in_stock, image, is_active) '
		.'VALUES ("'.$sDescription.'", '
		.'"'.$iPrice.'", '
		.'"'.$iInStock.'", '
		.'"'.$sImage.'", '
		.'"'.$iIsActive.'")'
	);
	$_GET['products']=1;
}


//product save
if (intval($_GET['productsave'])==1){
	$iID=intval($_GET['id']);
	$sDescription=$_GET['description'];
	$iPrice=intval($_GET['price']);
	$iInStock=intval($_GET['instock']);
	$sImage=$_GET['image'];
	$iIsActive=intval($_GET['isactive']);
	$aSave=myqu(
		'UPDATE '
		.$Conf["database"]["table_prefix"].'_product '
		.'SET '
		.'description="'.$sDescription.'", '
		.'price="'.$iPrice.'", '
		.'in_stock="'.$iInStock.'", '
		.'image="'.$sImage.'", '
		.'is_active="'.$iIsActive.'" '
		.'WHERE product_id="'.$iID.'"'
	);
	$_GET['products']=1;
}


//products page
if (intval($_GET['products'])==1){
	$aProducts=myqu(
		'SELECT product_id, thumbnail_imageserver_id, full_imageserver_id, '
		.'description, price, image, in_stock, is_active '
		.'FROM '.$Conf["database"]["table_prefix"].'_product '
		.'WHERE is_deleted IS NULL '
		.'ORDER BY description'
	);
	$aImageServers=myqu(
		'SELECT imageserver_id, description '
		.'FROM '.$Conf["database"]["table_prefix"].'_imageserver '
		.'ORDER BY description'
	);
	$iSize=sizeof($aImageServers);
	echo '<data>'.$sCRLF;
	echo '<imageservers>'.$sCRLF;
	for ($iCount=0;$iCount<$iSize;$iCount++){
		echo $sTab.'<imageserver_'.$iCount.'>'.$sCRLF;
		echo $sTab.$sTab.'<imageserver_id val="'
			.$aImageServers[$iCount]['imageserver_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<description val="'
			.$aImageServers[$iCount]['description'].'" />'.$sCRLF;
		echo $sTab.'</imageserver_'.$iCount.'>'.$sCRLF;
	}
	echo '</imageservers>'.$sCRLF;
	$iSize=sizeof($aProducts);
	echo '<products>'.$sCRLF;
	for ($iCount=0;$iCount<$iSize;$iCount++){
		echo $sTab.'<product_'.$iCount.'>'.$sCRLF;
		echo $sTab.$sTab.'<product_id val="'
			.$aProducts[$iCount]['product_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<description val="'
			.$aProducts[$iCount]['description'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<price val="'
			.$aProducts[$iCount]['price'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<image val="'
			.$aProducts[$iCount]['image'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<in_stock val="'
			.$aProducts[$iCount]['in_stock'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<is_active val="'
			.$aProducts[$iCount]['is_active'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<full_imageserver_id val="'
			.$aProducts[$iCount]['full_imageserver_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<thumbnail_imageserver_id val="'
			.$aProducts[$iCount]['thumbnail_imageserver_id'].'" />'.$sCRLF;
		echo $sTab.'</product_'.$iCount.'>'.$sCRLF;
	}
	echo '</products>'.$sCRLF;
	echo '</data>'.$sCRLF;
}






//categories main delete 
if (intval($_GET['categorymaindelete'])==1){
	$iID=$_GET['id'];
	$aDelete=myqu(
		'UPDATE '
		.$Conf["database"]["table_prefix"].'_category '
		.'SET is_deleted="1" '
		.'WHERE category_id="'.$iID.'"'
	);
	$_GET['categories']=1;
}


//categories main new 
if (intval($_GET['categorymainnew'])==1){
	$sValue=$_GET['value'];
	$aDuplicate=myqu(
		'SELECT category_id FROM '
		.$Conf["database"]["table_prefix"].'_category '
		.'WHERE description="'.$sValue.'"'
	);
	if ($aDuplicate[0]){
		exit;
	}
	$aSave=myqu(
		'INSERT INTO '
		.$Conf["database"]["table_prefix"].'_category '
		.'(description) VALUES ("'.$sValue.'")'
	);
	$aID=myqu(
		'SELECT category_id FROM '
		.$Conf["database"]["table_prefix"].'_category '
		.'WHERE description="'.$sValue.'"'
		);
	$aSave=myqu(
		'INSERT INTO '
		.$Conf["database"]["table_prefix"].'_category_x '
		.'(category_child_id) VALUES ("'.$aID[0]['category_id'].'")'
		);
	$_GET['categories']=1;
}


//categories main save
if (intval($_GET['categorymainsave'])==1){
	$iID=$_GET['id'];
	$sValue=$_GET['value'];
	$aSave=myqu(
		'UPDATE '
		.$Conf["database"]["table_prefix"].'_category '
		.'SET description="'.$sValue.'" '
		.'WHERE category_id="'.$iID.'"'
	);
	$_GET['categories']=1;
}


//categories page
if (intval($_GET['categories'])==1){
	$aMainCats=myqu(
		'SELECT A.category_child_id, B.description '
		.'FROM '.$Conf["database"]["table_prefix"].'_category_x A '
		.'INNER JOIN '.$Conf["database"]["table_prefix"].'_category B '
		.'ON A.category_child_id=B.category_id '
		.'WHERE A.category_parent_id IS NULL '
		.'AND B.is_deleted IS NULL '
		.'ORDER BY B.description'
	);
	$iSize=sizeof($aMainCats);
	echo '<categories>'.$sCRLF;
	for ($iCount=0;$iCount<$iSize;$iCount++){
		echo $sTab.'<category_'.$iCount.'>'.$sCRLF;
		echo $sTab.$sTab.'<id val="'
			.$aMainCats[$iCount]['category_child_id'].'" />'.$sCRLF;
		echo $sTab.$sTab.'<description val="'
			.$aMainCats[$iCount]['description'].'" />'.$sCRLF;
		$aSubCats=myqu(
			'SELECT A.category_child_id, B.description '
			.'FROM '.$Conf["database"]["table_prefix"].'_category_x A '
			.'INNER JOIN '.$Conf["database"]["table_prefix"].'_category B '
			.'ON A.category_child_id=B.category_id '
			.'WHERE A.category_parent_id="'
			.$aMainCats[$iCount]['category_child_id'].'" '
		);
		$iSizeSub=sizeof($aSubCats);
		for ($iCountSub=0;$iCountSub<$iSizeSub;$iCountSub++){
			echo $sTab.$sTab.'<subcategory_'.$iCountSub.'>'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<id val="'
				.$aSubCats[$iCountSub]['category_child_id'].'" />'.$sCRLF;
			echo $sTab.$sTab.$sTab.'<description val="'
				.$aSubCats[$iCountSub]['description'].'" />'.$sCRLF;
			echo $sTab.$sTab.'</subcategory_'.$iCountSub.'>'.$sCRLF;
		}
		echo $sTab.'</category_'.$iCount.'>'.$sCRLF;
	}
	echo '</categories>'.$sCRLF;
	exit;
}


// user logs out
if (intval($_GET['logout'])==1){
	$_SESSION=array();
	session_destroy();
	exit;
}


if (intval($_GET['init'])==1){
	$aConf=myqu(
		'SELECT category, keyname, keyvalue '
		.'FROM '.$Conf["database"]["table_prefix"].'_system'
	);
	date_default_timezone_set(findSQLValueFromKey($aConf,'system','timezone'));
	echo '<init>'.$sCRLF;
	echo $sTab.'<menu val="';
	echo findSQLValueFromKey($aConf,'admin','menu').'" />'.$sCRLF;
	echo $sTab.'<url val="'.$Conf['system']['web_url']
		.'/'.$Conf['system']['rootdir'].'/" />'.$sCRLF;
	echo '</init>'.$sCRLF;
}//end INIT

?>

