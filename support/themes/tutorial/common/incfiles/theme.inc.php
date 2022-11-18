<?php
//这是模板专属的函数文件,
//可以在制作模板添加模板专属的函数,
//避免对原系统进行修改,影响模板的迁移使用.

function wdja_cms_blog_index()
{
  mm_cntitle('');
  global $nskin;
  $slide_switch = ii_itake('global.support/themes/'.$nskin.':basic.slide-switch','lng');
  $tmpstr = ii_ireplace('global.module.index', 'tpl');
  if($slide_switch == 1) $tmpstr = str_replace('{$web-slide}', ii_ireplace('global.module.swiper-slide','tpl'), $tmpstr);
  else $tmpstr = str_replace('{$web-slide}', '<div style="margin:5px auto;overflow: hidden;"></div>', $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function wdja_cms_blog_list($genre,$class=0)
{
  global $conn, $nlng, $variable, $nurltype, $ncreatefiletype;
  $toffset = ii_get_num($_GET['offset']);
  $ngenre = $genre;
  $nclass = ii_get_num($class,0);
  $ndatabase_data = $variable[ii_cvgenre($ngenre) . '.ndatabase'];
  $nidfield_data = $variable[ii_cvgenre($ngenre) . '.nidfield'];
  $nfpre_data = $variable[ii_cvgenre($ngenre) . '.nfpre'];
  $nclstype = $variable[ii_cvgenre($ngenre) . '.nclstype'];
  $npagesize = $variable[ii_cvgenre($ngenre) . '.npagesize'];
  $nlisttopx = $variable[ii_cvgenre($ngenre) . '.nlisttopx'];
  $toffset = ii_get_num($_GET['offset']);
  $tmpstr = ii_itake('global.module.list-article', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase_data where " . ii_cfnames($nfpre_data,'lng') . "='$nlng' and " . ii_cfnames($nfpre_data,'hidden') . "=0";
  if($nclass != 0) $tsqlstr .= " and " . ii_cfnames($nfpre_data,'class') . "= $nclass";
  $tsqlstr .= " order by " . ii_cfnames($nfpre_data,'time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield_data;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> urlid = $nclass;
  $tcp -> pagesize = $npagesize;
  $tcp -> rslimit = $nlisttopx;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $img = $trs[ii_cfnames($nfpre_data,'image')];
      $con = $trs[ii_cfnames($nfpre_data,'content')];
      $tmptstr = str_replace('{$topic}', ii_htmlencode($trs[ii_cfnames($nfpre_data,'topic')]), $tmpastr);
      $tmptstr = str_replace('{$image}', mm_get_content_image($ngenre,$con,$img), $tmptstr);
      $tmptstr = str_replace('{$desc}', ii_left($trs[ii_cfnames($nfpre_data,'description')],130), $tmptstr);
      $tmptstr = str_replace('{$content}', ii_htmlencode($con), $tmptstr);
      $tmptstr = str_replace('{$classurl}',ii_get_actual_route($ngenre).'/'.ii_iurl('list',$trs[ii_cfnames($nfpre_data,'class')],$nurltype), $tmptstr);
      $tmptstr = str_replace('{$classname}',mm_get_sorttext($ngenre,$nlng,$trs[ii_cfnames($nfpre_data,'class')]), $tmptstr);
      $tmptstr = str_replace('{$tag}',api_tags_list($trs[$nidfield_data],$ngenre), $tmptstr);
      $tmptstr = str_replace('{$class}',$trs[ii_cfnames($nfpre_data,'class')], $tmptstr);
      $tmptstr = str_replace('{$count}',$trs[ii_cfnames($nfpre_data,'count')], $tmptstr);
      $tmptstr = str_replace('{$hidden}', ii_itake('global.sel_yesno.'.ii_get_num($trs[ii_cfnames($nfpre_data,'hidden')]), 'sel'), $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfnames($nfpre_data,'time')]), $tmptstr);
      $tmptstr = str_replace('{$id}', ii_get_num($trs[$nidfield_data]), $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagenum(), $tmpstr);
  $tmpstr = str_replace(WDJA_CINFO, $tmprstr, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function mm_get_advert($type){
  global $nskin;
  $tmpstr = '';
  $advert_switch = ii_itake('global.support/themes/'.$nskin.':extend.advert-switch','lng');
  if($advert_switch == 1){
      switch($type)
      {
        case 'index':
        default:
          $tmpstr = ii_itake('global.module.ad-index', 'tpl');
          break;
        case 'list':
          $tmpstr = ii_itake('global.module.ad-list','tpl');
          break;
        case 'detail':
          $tmpstr = ii_itake('global.module.ad-detail','tpl');
          break;
        case 'sidebar':
          $tmpstr = ii_itake('global.module.ad-sidebar','tpl');
          break;
      }
      $tmpstr = str_replace('{$nskin}', $nskin, $tmpstr);
      $tmpstr = ii_creplace($tmpstr);
  }
  return $tmpstr;
}

function mm_get_sidebar($type){
  global $nskin;
  $advert_switch = ii_itake('global.support/themes/'.$nskin.':extend.advert-switch','lng');
  switch($type)
  {
    case 'search':
      $tmpstr = ii_ireplace('global.module.sidebar-search','tpl');
      break;
    case 'sort':
      $tmpstr = ii_ireplace('global.module.sidebar-sort','tpl');
      break;
    case 'new':
      $tmpstr = ii_ireplace('global.module.sidebar-new','tpl');
      break;
    case 'hot':
      $tmpstr = ii_ireplace('global.module.sidebar-hot','tpl');
      break;
    case 'roll':
      $tmpstr = mm_get_advert('sidebar-roll');
      break;
    case 'tag':
      $tmpstr = ii_ireplace('global.module.sidebar-tag','tpl');
      break;
    default:
      $tmpstr = ii_ireplace('global.module.sidebar-search','tpl');
      break;
  }
  return $tmpstr;
}