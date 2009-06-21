<?php
/*
Plugin Name: Driggle Nachrichten
Plugin URI: http://de.driggle.com/
Description: Die Schlagzeilen des Tages direkt im Blog anzeigen.
Author: Philip Matesanz
Version: 1.0
Author URI: http://de.driggle.com/

Driggle Nachrichten wurde unter der GNU General Public License (GPL) veröffentlicht.
http://www.gnu.org/licenses/gpl.txt
*/

$DriggleNewsVersion = '1.0';
$DriggleNewsCategorys = array(	'Politik'=>1,
								'Wirtschaft'=>2,
								'Technik &amp; Wissen'=>3,
								'Sport'=>4,
								'Unterhaltung'=>5	);
$DriggleNewsDefaults = array(	'Title'=>'Nachrichten',
								'Order'=>1,
								'Categorys'=>array(1,2,3,4,5),
								'Limit'=>6	);

function DriggleNewsInit()
{
	if (!function_exists('register_sidebar_widget') || !function_exists('register_widget_control'))
		return;
		
    register_sidebar_widget('Driggle Nachrichten', 'DriggleNewsOutput');
    register_widget_control('Driggle Nachrichten', 'DriggleNewsControl');
}

function DriggleNewsDefaultsCheck(&$Options)
{
	global $DriggleNewsDefaults;
	
	if(count($Options)!=count($DriggleNewsDefaults))
	{
		$Options = $DriggleNewsDefaults;
		return;
	}
	if($Options['Title']=='')
	{
		$Options['Title']=$DriggleNewsDefaults['Title'];
	}
	if($Options['Categorys']==array())
	{
		$Options['Categorys']=$DriggleNewsDefaults['Categorys'];
	}
	if($Options['Limit']==0)
	{
		$Options['Limit']=$DriggleNewsDefaults['Limit'];
	}
	if($Options['Order']==0)
	{
		$Options['Order']=$DriggleNewsDefaults['Order'];
	}
}

function DriggleNewsSecureArray($Array)
{
	$NewArray = array();
	
	foreach($Array as $Item)
	{
		$NewArray[intval($Item)] = intval($Item);
	}
	
	return $NewArray;
}

function DriggleNewsOutput($Args)
{
	global $DriggleNewsVersion;
	
	$Options = get_option('DriggleNews');

	DriggleNewsDefaultsCheck(&$Options);
	
	print $Args['before_widget'].
            $Args['before_title'].
			$Options['Title'].
			$Args['after_title'].
			'<ul>'.
			'<script type="text/javascript" src="http://s.driggle.com/app/widget?a=wp&amp;v='.$DriggleNewsVersion.'&amp;c='.join(',',$Options['Categorys']).'&amp;o='.$Options['Order'].'&amp;l='.$Options['Limit'].'&amp;cc=de"></script>'.
			'<script type="text/javascript">__Driggle_News_BeforeItem = \'<li>\'; __Driggle_News_AfterItem = \'</li>\'; __Driggle_News_Init();</script>'.
			'<li style="list-style-type: none;">Von: <a href="http://de.driggle.com/">Driggle Nachrichten</a></li>'.
			'</ul>'.
		  $Args['after_widget'];
}

function DriggleNewsControl()
{
	global $DriggleNewsCategorys;
	
	$NewOptions = array();
					 
	$Options = get_option('DriggleNews');
	
	DriggleNewsDefaultsCheck(&$Options);
	
	if(isset($_POST['DriggleNewsSubmit']))
	{
		if(strlen($_POST['DriggleNewsTitle'])>0)
			$NewOptions['Title'] = mysql_escape_string(htmlspecialchars($_POST['DriggleNewsTitle']));
		else
			$NewOptions['Title'] = $Options['Title'];
			
		if(is_array($_POST['DriggleNewsCategorys']) && count($_POST['DriggleNewsCategorys'])>0)
			$NewOptions['Categorys'] = DriggleNewsSecureArray($_POST['DriggleNewsCategorys']);
		else
			$NewOptions['Categorys'] = $Options['Categorys'];
			
		if(in_array(intval($_POST['DriggleNewsOrder']),array(1,2)))
			$NewOptions['Order'] = intval($_POST['DriggleNewsOrder']);
		else
			$NewOptions['Order'] = $Options['Order'];
			
		if(intval($_POST['DriggleNewsLimit'])>0 && intval($_POST['DriggleNewsLimit'])<11)
			$NewOptions['Limit'] = intval($_POST['DriggleNewsLimit']);
		else
			$NewOptions['Limit'] = $Options['Limit'];
			
		update_option('DriggleNews', $NewOptions);
	}
	
	$DriggleNewsCategoryOptions = '';

	foreach($DriggleNewsCategorys as $DriggleNewsCategory=>$DriggleNewsCategoryKey)
	{
		$DriggleNewsCategoryOptions .= '<option value="'.$DriggleNewsCategoryKey.'"'.((isset($Options['Categorys']) && in_array($DriggleNewsCategoryKey,$Options['Categorys'])) ? ' selected="selected"' : '').'>'.$DriggleNewsCategory.'</option>';
	}
	
	print 	'<div>'.
				'<p><label for="DriggleNewsTitle">Titel: <input type="text" id="DriggleNewsTitle" name="DriggleNewsTitle" value="'.$Options['Title'].'" /></label></p>'.
				'<p><table style="border:0;"><tr valign="top" style="vertical-align:top"><td><label for="DriggleNewsCategorys">Kategorien:</label></td><td><select multiple="multiple" size="5" style="height:100px" name="DriggleNewsCategorys[]" id="DriggleNewsCategorys">'.$DriggleNewsCategoryOptions.'</select></td></tr></table></p>'.
				'<p><label for="DriggleNewsLimit">Anzahl Meldungen: <input type="text" id="DriggleNewsLimit" name="DriggleNewsLimit" value="'.$Options['Limit'].'" style="width:30px; text-align:center" /></label></p>'.
				'<p>Sortierung: <label for="DriggleNewsOrderRelevancy"><input type="radio" name="DriggleNewsOrder" id="DriggleNewsOrderRelevancy" value="1"'.((isset($Options['Order']) && $Options['Order']==1) ? ' checked="checked"' : '').' /> Relevanz</label> <label for="DriggleNewsOrderDate"><input type="radio" name="DriggleNewsOrder" id="DriggleNewsOrderDate" value="2"'.((isset($Options['Order']) && $Options['Order']==2) ? ' checked="checked"' : '').' /> Datum</label></p>'.
				'<input type="hidden" name="DriggleNewsSubmit" id="DriggleNewsSubmit" value="true" />'.
			'</div>';
}

add_action('plugins_loaded', 'DriggleNewsInit');

?> 