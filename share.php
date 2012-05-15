<?php
/*
Plugin Name: Simple Share For Chinese Social Sites
Plugin URI: http://blog.centilin.com/everything_it/web-design/wordpress/simple-share-for-chinese-social-sites/ ‎
Description: This is a simplified version of the Sinoshare plugin with 40 sharing options. I have converted the plugin so it runs as jQuery instead of Prototype. After installation, you will see a 分享此文章 at the end of the content. Next version will include a shortcode and more configuration options.
Version: 120515
Author: Angela Zou
Author URI: http://blog.centilin.com
    Copyright 2010  Angela Zou  (email : angela380in@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Setting the Options Page
register_activation_hook(__FILE__, 'simple_share_init');
function simple_share_init() {
	update_option("simple_share_text", "分享此文章");
	update_option("simple_share_default_styling", "1");
}

add_action('admin_menu', 'simple_share_admin_menu');
function simple_share_admin_menu() {
	add_options_page('Simple Share Options', 'Simple Share', 'manage_options', 'simple-share', 'simple_share_admin_output');
	add_action('admin_init', 'simple_share_register_plugin_settings');
}

function simple_share_register_plugin_settings() {
	register_setting('simple_share_options', 'simple_share_head_or_foot');
	register_setting('simple_share_options', 'simple_share_text');
	register_setting('simple_share_options', 'simple_share_default_styling');
	register_setting('simple_share_options', 'simple_share_custom_css');
	
	add_settings_section('simple_share_options', 'General Options', 'simple_share_options_code', 'simple_share_options');
	add_settings_field('simple_share_head_or_foot', 'Place At Foot (Check to place at head)', 'simple_share_head_or_foot_code', 'simple_share_options', 'simple_share_options');
	add_settings_field('simple_share_text', 'Text For Sharing', 'simple_share_text_code', 'simple_share_options', 'simple_share_options');
	add_settings_field('simple_share_default_styling', 'Use Default Styling', 'simple_share_default_styling_code', 'simple_share_options', 'simple_share_options');
	add_settings_field('simple_share_custom_css', '', 'simple_share_custom_css_code', 'simple_share_options', 'simple_share_options');	
}

function simple_share_options_code() {
	echo _e('Settings for Simple Share Plugin');
}
function simple_share_head_or_foot_code() {
	echo '<input id="simple_share_head_or_foot" name="simple_share_head_or_foot" type="checkbox" value="1" ' . checked( get_option("simple_share_head_or_foot"), 1, false ) . '" /><br />';
}
function simple_share_text_code() {
	echo '<input id="simple_share_text" name="simple_share_text" type="text" value="' . get_option("simple_share_text") . '" /><br />';
}
function simple_share_default_styling_code() {
	echo '<input id="simple_share_default_styling" name="simple_share_default_styling" type="checkbox" value="1" ' . checked( get_option("simple_share_default_styling"), 1, false ) . ' onchange="javascript:tohide()" /><br />';
}
function simple_share_custom_css_code() {
	echo '<div id="tohide"><p>Your Custom CSS. Use #share_block for the entire area. Use #share_link for text link. Use #share_menu to style the dropdown menu. Use .share_image for the thumbnail, and .share_title for the share site text.</p><textarea id="simple_share_custom_css" name="simple_share_custom_css" cols=90 rows=8>' . get_option("simple_share_custom_css") . '</textarea><br /></div>
	<script type="text/javascript">
		window.onload = tohide();
		//Hide the Textbox
		function tohide() {
			var box = document.getElementById("simple_share_default_styling");
			var text = document.getElementById("tohide");
			if(!box.checked) { text.style.display = "block"; }
			else { text.style.display = "none"; }
		}
	</script>';
}

function simple_share_admin_output() {
	?>
	<div class="wrap">
		<h2><?php echo _e('Simple Share General Options'); ?></h2>
		<form method="post" action="options.php">
			<?php
				settings_fields('simple_share_options');
				do_settings_sections('simple_share_options');
			?>
		<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
		</form>
	</div>
	<?php
}


//Core Code
add_filter('the_content', 'share');
function share($content) {
	$title = single_post_title('', FALSE);
	$title = str_replace("'", "\'", $title);
	$url = "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	$path = site_url('/wp-content/plugins/simple-share-for-chinese-social-sites/assets/');

	$social_sites = array(
		'bookmark' => array(
			'name' => '收藏夹'
			, 'url' => "{$url}"
			, 'cl' => 'jQueryBookmark'
		)
		,'baidu' => array(
			'name' => '百度搜藏'
			, 'url' => "http://cang.baidu.com/do/add?it={$title}&iu={$url}&dc=&fr=ien#nw=1"
		)
		,'qqzone' => array(
			'name' => 'QQ空间'
			, 'url' => "http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url={$url}"
		)
		,'sinaweibo'=>array(
			'name' => '新浪微博'
			, 'url' => "http://v.t.sina.com.cn/share/share.php?title={$title}&url={$url}"
		)
		,'163'=>array(
			'name' => '网易微博'
			, 'url' => "http://t.163.com/article/user/checkLogin.do?source=网易微博&info={$title}{$url}"
		)
		,'sohubai'=>array(
			'name' => '搜狐白社会'
			, 'url' => "http://bai.sohu.com/share/blank/add.do?link={$url}&title={$title}"
		)
		, 'renren' => array(
			'name' => '人人网'
			, 'url' => "http://share.renren.com/share/buttonshare.do?link={$url}&title={$title}"
		)
		, 'kaixin' => array(
			'name' => '开心网'
			, 'url' => "http://www.kaixin001.com/repaste/share.php?rtitle={$title}&rurl={$url}"
		)
		, 'googlebuzz' => array(
			'name' => '谷歌Buzz'
			, 'url' => "http://www.google.com/reader/link?url={$url}"
		)
		, 'google_bmarks' => array(
			'name' => '谷歌收藏'
			, 'url' => "http://www.google.com/bookmarks/mark?op=add&bkmk={$url}&title={$title}"
		)
		, 'douban' => array(
			'name' => '豆瓣网'
			, 'url' => "http://www.douban.com/recommend/?url={$url}&title={$title}&sel=&v=1"
		)
		, 'taojianghu' => array(
			'name' => '淘江湖'
			, 'url' => "http://share.jianghu.taobao.com/share/addShare.htm?url={$url}"
		)
		, 'zhuaxia' => array(
			'name' => '抓虾'
			, 'url' => "http://www.zhuaxia.com/add_channel.php?url={$url}"
		)
		, 'xianguo' => array(
			'name' => '鲜果'
			, 'url' => "http://xianguo.com/service/submitfav/?link={$url}&title={$title}"
		)
		, 'youdao' => array(
			'name' => '有道'
			, 'url' => "http://shuqian.youdao.com/manage?a=popwindow&url={$url}&title={$title}"
		)
		, '9dian' => array(
			'name' => '九点'
			, 'url' => "http://9.douban.com/recommend/?url={$url}&title={$title}"
		)
		, 'twitter' => array(
			'name' => 'Twitter'
			, 'url' => "http://twitter.com/home?status={$title} {$url}"
		)
		, 'facebook' => array(
			'name' => 'Facebook'
			, 'url' => "http://www.facebook.com/share.php?u={$url}&t={$title}"
		)
		, 'yahoo_myweb' => array(
			'name' => '雅虎收藏'
			, 'url' => "http://myweb.cn.yahoo.com/popadd.html?url={$url}&title={$title}"
		)
		, 'baidukongjian' => array(
			'name' => '百度空间'
			, 'url' => "http://apps.hi.baidu.com/share/?url={$url}&title={$title}"
		)
		, 'qqbookmark' => array(
			'name' => 'QQ书签'
			, 'url' => "http://shuqian.qq.com/post?title={$title}&uri={$url}"
		)
		, 'sina-vivi' => array(
			'name' => '新浪收藏'
			, 'url' => "http://vivi.sina.com.cn/collect/icollect.php?pid=28&title={$title}&url={$url}"
		)
		, '139shuo' => array(
			'name' => '139说客'
			, 'url' => "http://www.139.com/share/share.php?url={$url}&title={$title}"
		)
		, 'digu' => array(
			'name' => '嘀咕'
			, 'url' => "http://www.diguff.com/diguShare/bookMark_FF.jsp?title={$title}&url={$url}"
		)
		, 'zuosha' => array(
			'name' => '做啥'
			, 'url' => "http://zuosa.com/collect/Collect.aspx?t={$title}&u={$url}"
		)
		, 'renjian' => array(
			'name' => '人间'
			, 'url' => "http://renjian.com/outside/share_link.xhtml?link={$url}"
		)
		, 'follow5' => array(
			'name' => 'Follow5'
			, 'url' => "http://www.follow5.com/f5/jsp/plugin/5share/5ShareLogin.jsp?title={$title}&url={$url}"
		)
		, 'hexun' => array(
			'name' => '和讯网摘'
			, 'url' => "http://bookmark.hexun.com/post.aspx?title={$title}&url={$url}"
		)
		, 'myspace' => array(
			'name' => 'Myspace'
			, 'url' => "http://www.myspace.com/Modules/PostTo/Pages/default.aspx/?u={$url}&t={$title}"
		)
		, 'chuangye' => array(
			'name' => '创业邦'
			, 'url' => "http://u.cyzone.cn/share.php?title={$title}&url={$url}"
		)
		, 'zhongjin' => array(
			'name' => '中金微博'
			, 'url' => "http://t.cnfol.com/share.php?title={$title}&url={$url}"
		)
		, 'ruolin' => array(
			'name' => '若邻网'
			, 'url' => "http://share.wealink.com/share/add/?title={$title}&url={$url}"
		)
		, 'leshou' => array(
			'name' => '乐收网'
			, 'url' => "http://leshou.com/post?act=shou&reuser=&title={$title}&url={$url}&intro=&tags=&tool=1"
		)
		, 'qike' => array(
			'name' => '奇客发现'
			, 'url' => "http://www.diglog.com/submit.aspx?url={$url}&title={$title}&description="
		)
		, 'tongxue' => array(
			'name' => '同学微博'
			, 'url' => "http://share.tongxue.com/share/buttonshare.php?link={$url}&title={$title}"
		)
		, 'wake' => array(
			'name' => '挖客网'
			, 'url' => "http://www.waakee.com/submit.php?url={$url}&title={$title}"
		)
		, 'jiuxihuan' => array(
			'name' => '就喜欢'
			, 'url' => "http://www.9fav.com/profile/user_url/add?t={$title}&u={$url}&tag=&d="
		)
		, '115shoucang' => array(
			'name' => '115收藏'
			, 'url' => "http://fav.115.com/?ac=add&title={$title}&url={$url}"
		)
		, 'digg' => array(
			'name' => 'Digg'
			, 'url' => "http://digg.com/submit?phase=2&url={$url}&title={$title}"
		)
		, 'delicious' => array(
			'name' => 'Delicious'
			, 'url' => "http://del.icio.us/post?url={$url}&title={$title}"
		)
	);
	

	$output = '<link rel="stylesheet" href="' . $path . 'share.css" type="text/css">
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery("a#share_link").click(function () {
					jQuery("ul#share_menu").slideToggle("medium");
				});
				
					jQuery("a.jQueryBookmark").click(function(e){
					e.preventDefault();
					var bookmarkUrl = this.href;
					var bookmarkTitle = this.title;
					if (window.sidebar) { // For Mozilla Firefox Bookmark
						window.sidebar.addPanel(bookmarkTitle, bookmarkUrl,"");
					} else if( window.external || document.all) { // For IE Favorite
						window.external.AddFavorite( bookmarkUrl, bookmarkTitle);

					} else if(window.opera) { // For Opera Browsers
						jQuery("a.jQueryBookmark").attr("href",bookmarkUrl);
						jQuery("a.jQueryBookmark").attr("title",bookmarkTitle);
						jQuery("a.jQueryBookmark").attr("rel","sidebar");
					} else { // for other browsers which does not support
						alert("Bookmark Failed. Please Bookmark Manually.");
						return false;
					}
				});

			});

			jQuery(document).ready(function(){
			});
		</script>';
	if(get_option("simple_share_default_styling") == '1') { //If using default styling
		$output .= '<style type="text/css">
			​a#share_link {
				color: white;
				font-size: 1.2em;
				line-height: 20px;
				font-weight: lighter;
				text-decoration: none;
				text-shadow: 0 1px 0 #2A4F83;

				padding: 5px 7px;
				float: left;
				display: block;
				position: relative;

				background: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #597DB1), color-stop(100%, #3A6198));
				background: -webkit-linear-gradient(#597DB1, #3A6198);
				background: -moz-linear-gradient(#597DB1, #3A6198);
				background: -o-linear-gradient(#597DB1, #3A6198);
				-pie-background: linear-gradient(#597DB1, #3A6198);

			    border: 1px solid #3B639B;
			    -moz-border-radius: 6px;
			    -webkit-border-radius: 6px;
			    -o-border-radius: 6px;
			    -ms-border-radius: 6px;
			    -khtml-border-radius: 6px;
			    border-radius: 6px;
			    -moz-box-shadow: inset 0 1px 0 0 #7D99BF;
			    -webkit-box-shadow: inset 0 1px 0 0 #7D99BF;
			    -o-box-shadow: inset 0 1px 0 0 #7D99BF;
			    box-shadow: inset 0 1px 0 0 #7D99BF;
			}

			a#share_link:hover {
				background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #365A8C), color-stop(100%, #51719E));
				background-image: -webkit-linear-gradient(#365A8C, #51719E);
				background-image: -moz-linear-gradient(#365A8C, #51719E);
				background-image: -o-linear-gradient(#365A8C, #51719E);
				-pie-background: linear-gradient(#365A8C, #51719E);

				-moz-box-shadow: inset 0 1px 0 0 #728CB1;
				-webkit-box-shadow: inset 0 1px 0 0 #728CB1;
				-o-box-shadow: inset 0 1px 0 0 #728CB1;
				box-shadow: inset 0 1px 0 0 #728CB1;
				cursor: pointer;
			}

			​a#share_link:active {
				-moz-box-shadow: inset 0 0 8px 4px #335380, inset 0 0 8px 4px #335380, 0 1px 1px 0 #EEE;
				-webkit-box-shadow: inset 0 0 8px 4px #335380, inset 0 0 8px 4px #335380, 0 1px 1px 0 #EEE;
				-o-box-shadow: inset 0 0 8px 4px #335380, inset 0 0 8px 4px #335380, 0 1px 1px 0 #EEE;
				box-shadow: inset 0 0 8px 4px #335380, inset 0 0 8px 4px #335380, 0 1px 1px 0 #EEE;
				border: 1px solid #355A8D;
			}​
		</style>';
	} else {
		$output .= '<style type="text/css">' . get_option("simple_share_custom_css") . '</style>';
	}
	
	$output .= '<div id="share_block"><h4><a href="#share_link" name="share_link" id="share_link" title="' . $title . '">' . get_option("simple_share_text") . '</a></h4><ul id="share_menu">';
	foreach ($social_sites as $key => $value) {
		$output .= '<li><span class="share_image"><img src="' . $path . $key . '.gif" title="' . $title . '" /></span><span class="share_title"><a href="' . $value["url"] . '"';
		if (isset($value["cl"])) $output .= ' class="' . $value["cl"] . '"';
		$output .= '">' . $value["name"] . '</a></span></li>';
	}
	$output .= '</ul></div>';

	if(get_option("simple_share_head_or_foot") == '1') {
		$content = $output . $content;
	} else { $content .= $output; }
	
	return $content;
}
?>