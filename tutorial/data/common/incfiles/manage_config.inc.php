<?php
//****************************************************
// WDJA CMS Power by wdja.net
// Email: admin@wdja.net
// Web: http://www.wdja.net/
//****************************************************
wdja_cms_admin_init();
$nurltype = 0;
$nsearch = 'topic,id';
$ncontrol = 'select,hidden,good,delete';

function pp_manage_navigation()
{
  return ii_ireplace('manage.navigation', 'tpl');
}

function pp_manage_batch_menu()
{
  return ii_ireplace('manage.batch_menu', 'tpl');
}

function wdja_cms_interface_check_topic()
{
  global $ngenre;
  $bool = false;
  $tid = ii_get_safecode($_GET['id']);
  $ttopic = ii_get_safecode($_GET['topic']);
  if (!ii_isnull($tid)) $bool = mm_search_field($ngenre,$ttopic,'topic',$tid);
  else $bool = mm_search_field($ngenre,$ttopic,'topic');
  if ($bool) echo '1';
  else echo '0';
  exit;
}

function wdja_cms_admin_manage_adddisp()
{
  global $ngenre, $slng;
  global $conn;
  global $ndatabase, $nidfield, $nfpre, $nsaveimages;
  $tbackurl = $_GET['backurl'];
  $tckstr = 'topic:' . ii_itake('global.lng_config.topic', 'lng');
  $tary = explode(',', $tckstr);
  $Err = array();
  foreach ($tary as $val)
  {
    $tvalary = explode(':', $val);
    if (ii_isnull($_POST[$tvalary[0]])) $Err[count($Err)] = str_replace('[]', '[' . $tvalary[1] . ']', ii_itake('global.lng_error.insert_empty', 'lng'));
  }
  if (is_array($Err) && count($Err) > 0) wdja_cms_admin_msg($Err[0], $tbackurl, 1);
  $ttutorial_id = ii_get_safecode($_POST['tutorial_id']);
  $tischapter = ii_get_num($_POST['ischapter']);
  $tchapter_id = ($tischapter == 0 || ii_isnull($_POST['chapter_id'])) ? 0 : ii_get_num($_POST['chapter_id']);
  $tvuser = ii_get_num($_POST['vuser']);
  if ($tvuser == 1) $tvuid = ii_get_num(mm_get_rand_vuser());
  else $tvuid = 0;
  $timage = ii_left(ii_cstr($_POST['image']), 255);
  if (mm_search_field($ngenre,ii_cstr($_POST['ucode']),'ucode') && !ii_isnull($_POST['ucode'])) wdja_cms_admin_msg(ii_itake('manage.ucode_failed', 'lng'), $tbackurl, 1);
  if ($nsaveimages == '1') $tcontent = ii_left(ii_cstr(saveimages($_POST['content'])), 100000);
  else $tcontent =ii_left(ii_cstr($_POST['content']), 100000);
  $tcontent_atts_list = ii_left(ii_cstr($_POST['content_atts_list']), 10000);
  $tswitch = ii_get_num($_POST['timer_switch']);
  $tevent = ii_get_num($_POST['event']);//0发布,1隐藏
  if ($tswitch == 1) {
    if ($tevent == 1) $thidden = 0;
    else $thidden = 1;
  }
  else $thidden = ii_get_num($_POST['hidden']);
    $tsqlstr = "insert into $ndatabase (
    " . ii_cfname('topic') . ",
    " . ii_cfname('titles') . ",
    " . ii_cfname('keywords') . ",
    " . ii_cfname('description') . ",
    " . ii_cfname('file') . ",
    " . ii_cfname('image') . ",
    " . ii_cfname('content') . ",
    " . ii_cfname('content_atts_list') . ",
    " . ii_cfname('ucode') . ",
    " . ii_cfname('vuser') . ",
    " . ii_cfname('vuid') . ",
    " . ii_cfname('time') . ",
    " . ii_cfname('update') . ",
    " . ii_cfname('tutorial_id') . ",
    " . ii_cfname('ischapter') . ",
    " . ii_cfname('chapter_id') . ",
    " . ii_cfname('hidden') . ",
    " . ii_cfname('good') . ",
    " . ii_cfname('tpl') . ",
    " . ii_cfname('gourl') . ",
    " . ii_cfname('lng') . "
    ) values (
    '" . ii_left(ii_cstr($_POST['topic']), 250) . "',
    '" . ii_left(ii_cstr($_POST['titles']), 250) . "',
    '" . ii_left(ii_cstr($_POST['keywords']), 250) . "',
    '" . ii_left(ii_cstr($_POST['description']), 250) . "',
    '" . ii_left(ii_cstr($_POST['file']), 255). "',
    '$timage',
    '$tcontent',
    '$tcontent_atts_list',
    '" . ii_left(ii_cstr($_POST['ucode']), 50) . "',
    '$tvuser',
    '$tvuid',
    '" . ii_now() . "',
    '" . ii_now() . "',
    '" . $ttutorial_id . "',
    '" . $tischapter . "',
    '" . $tchapter_id . "',
    " . $thidden . ",
    " . ii_get_num($_POST['good']) . ",
    '" . ii_left(ii_cstr($_POST['tpl']), 50) . "',
    '" . ii_left(ii_cstr($_POST['gourl']), 255) . "',
    '$slng'
    )";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      $upfid = ii_conn_insert_id($conn);
      api_save_fields($upfid);
      api_save_tags($upfid);
      api_save_timer($upfid);
      if (ii_get_num($_POST['hidden']) ==0 && ii_get_num($_POST['timer_switch']) ==0) mm_baidu_push('urls',$ngenre,ii_left(ii_cstr($_POST['topic']), 250),$upfid);
      uu_upload_update_database_note($ngenre, $tcontent_atts_list, 'content_atts', $upfid);
      wdja_cms_admin_msg(ii_itake('global.lng_public.add_succeed', 'lng'), $tbackurl, 1);
    }
    else wdja_cms_admin_msg(ii_itake('global.lng_public.add_failed', 'lng'), $tbackurl, 1);
}

function wdja_cms_admin_manage_editdisp()
{
  global $ngenre;
  global $conn;
  global $ndatabase, $nidfield, $nfpre, $nsaveimages;
  $tbackurl = $_GET['backurl'];
  $tckstr = 'topic:' . ii_itake('global.lng_config.topic', 'lng');
  $tary = explode(',', $tckstr);
  $Err = array();
  foreach ($tary as $val)
  {
    $tvalary = explode(':', $val);
    if (ii_isnull($_POST[$tvalary[0]])) $Err[count($Err)] = str_replace('[]', '[' . $tvalary[1] . ']', ii_itake('global.lng_error.insert_empty', 'lng'));
  }
  if (is_array($Err) && count($Err) > 0) wdja_cms_admin_msg($Err[0], $tbackurl, 1);
  $tischapter = ii_get_num($_POST['ischapter']);
  $tchapter_id = ($tischapter == 0 || ii_isnull($_POST['chapter_id'])) ? 0 : ii_get_num($_POST['chapter_id']);
  $tid = ii_get_num($_GET['id']);
  $tvuser = ii_get_num($_POST['vuser']);
  $tvuid = mm_get_field($ngenre,$tid,'vuid');
  if ($tvuser == 1 && $tvuid == 0) $tvuid = ii_get_num(mm_get_rand_vuser());
  $timage = ii_left(ii_cstr($_POST['image']), 255);
  if (mm_search_field($ngenre,ii_cstr($_POST['ucode']),'ucode',$tid) && !ii_isnull($_POST['ucode'])) wdja_cms_admin_msg(ii_itake('manage.ucode_failed', 'lng'), $tbackurl, 1);
  if ($nsaveimages == '1') $tcontent = ii_left(ii_cstr(saveimages($_POST['content'])), 100000);
  else $tcontent = ii_left(ii_cstr($_POST['content']), 100000);
  $tcontent_atts_list = ii_left(ii_cstr($_POST['content_atts_list']), 10000);
    $tsqlstr = "update $ndatabase set
    " . ii_cfname('topic') . "='" . ii_left(ii_cstr($_POST['topic']), 250) . "',
    " . ii_cfname('titles') . "='" . ii_left(ii_cstr($_POST['titles']), 250) . "',
    " . ii_cfname('keywords') . "='" . ii_left(ii_cstr($_POST['keywords']), 250) . "',
    " . ii_cfname('description') . "='" . ii_left(ii_cstr($_POST['description']), 250) . "',
    " . ii_cfname('file') . "='" . ii_left(ii_cstr($_POST['file']), 250) . "',
    " . ii_cfname('image') . "='$timage',
    " . ii_cfname('content') . "='$tcontent',
    " . ii_cfname('content_atts_list') . "='$tcontent_atts_list',
    " . ii_cfname('ucode') . "='" . ii_left(ii_cstr($_POST['ucode']), 50) . "',
    " . ii_cfname('vuser') . "='$tvuser',
    " . ii_cfname('vuid') . "='$tvuid',
    " . ii_cfname('time') . "='" . ii_get_date(ii_cstr($_POST['time'])) . "',
    " . ii_cfname('update') . "='" . ii_now() . "',
    " . ii_cfname('ischapter') . "='" . $tischapter . "',
    " . ii_cfname('chapter_id') . "='" . $tchapter_id . "',
    " . ii_cfname('count') . "=" . ii_get_num($_POST['count']) . ",
    " . ii_cfname('hidden') . "=" . ii_get_num($_POST['hidden']) . ",
    " . ii_cfname('good') . "=" . ii_get_num($_POST['good']) . ",
    " . ii_cfname('tpl') . "='" . ii_left(ii_cstr($_POST['tpl']), 50) . "',
    " . ii_cfname('gourl') . "='" . ii_left(ii_cstr($_POST['gourl']), 255) . "'
    where $nidfield=$tid";
    $trs = ii_conn_query($tsqlstr, $conn);
    if ($trs)
    {
      $upfid = $tid;
      api_update_fields($upfid);
      api_update_tags($upfid);
      api_update_timer($upfid);
      if (ii_get_num($_POST['hidden']) ==0 && ii_get_num($_POST['timer_switch']) ==0) {
      if (mm_search_baidu(array('genre' => $ngenre,'gid' => $upfid))) mm_baidu_push('update',$ngenre,ii_left(ii_cstr($_POST['topic']), 250),$upfid);
      else mm_baidu_push('urls',$ngenre,ii_left(ii_cstr($_POST['topic']), 250),$upfid);
      }else{
        mm_baidu_push('del',$ngenre,ii_left(ii_cstr($_POST['topic']), 250),$upfid);
      }
      uu_upload_update_database_note($ngenre, $tcontent_atts_list, 'content_atts', $upfid);
      wdja_cms_admin_msg(ii_itake('global.lng_public.edit_succeed', 'lng'), $tbackurl, 1);
    }
    else wdja_cms_admin_msg(ii_itake('global.lng_public.edit_failed', 'lng'), $tbackurl, 1);
}

function wdja_cms_admin_manage_action()
{
  global $ndatabase, $nidfield, $nfpre, $ncontrol;
  switch($_GET['action'])
  {
    case 'add':
      wdja_cms_admin_manage_adddisp();
      break;
    case 'edit':
      wdja_cms_admin_manage_editdisp();
      break;
    case 'delete':
      wdja_cms_admin_deletedisp($ndatabase, $nidfield);
      break;
    case 'control':
      wdja_cms_admin_controldisp($ndatabase, $nidfield, $nfpre, $ncontrol);
      break;
    case 'batch_shift':
      wdja_cms_admin_batch_shiftdisp($ndatabase, $nidfield, $nfpre);
      break;
    case 'batch_delete':
      wdja_cms_admin_batch_deletedisp($ndatabase, $nidfield, $nfpre);
      break;
    case 'upload':
      uu_upload_files();
      break;
  }
}

function wdja_cms_admin_manage_add()
{
  global $nupsimg, $nupsimgs;
  $tmpstr = ii_itake('manage.add', 'tpl');
  $tmpstr = str_replace('{$upsimg}', $nupsimg, $tmpstr);
  $tmpstr = str_replace('{$upsimgs}', $nupsimgs, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function wdja_cms_admin_manage_edit()
{
  global $conn;
  global $ndatabase, $nidfield, $nfpre, $nupsimg, $nupsimgs;
  $tid = ii_get_num($_GET['id']);
  $tsqlstr = "select * from $ndatabase where $nidfield=$tid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if ($trs)
  {
    $tmpstr = ii_itake('manage.edit', 'tpl');
    foreach ($trs as $key => $val)
    {
      $tkey = ii_get_lrstr($key, '_', 'rightr');
      $GLOBALS['RS_' . $tkey] = $val;
      $tmpstr = str_replace('{$' . $tkey . '}', ii_htmlencode($val), $tmpstr);
    }
    $tmpstr = str_replace('{$id}', $trs[$nidfield], $tmpstr);
    $tmpstr = str_replace('{$upsimg}', $nupsimg, $tmpstr);
    $tmpstr = str_replace('{$upsimgs}', $nupsimgs, $tmpstr);
    $tmpstr = ii_creplace($tmpstr);
    return $tmpstr;
  }
  else mm_client_alert(ii_itake('global.lng_public.sudd', 'lng'), -1);
}

function wdja_cms_admin_manage_list()
{
  global $conn, $slng;
  global $ngenre, $nclstype, $npagesize, $nlisttopx;
  global $ndatabase, $nidfield, $nfpre;
  global $sort_database, $sort_idfield, $sort_fpre;
  $toffset = ii_get_num($_GET['offset']);
  $ttutorial_id = ii_get_num($_GET['tutorial_id']);
  $tchapter_id = ii_get_num($_GET['chapter_id']);
  $search_field = ii_get_safecode($_GET['field']);
  $search_keyword = ii_get_safecode($_GET['keyword']);
  $tmpstr = ii_itake('manage.list', 'tpl');
  $tmpastr = ii_ctemplate($tmpstr, '{@recurrence_ida}');
  $tmprstr = '';
  $tsqlstr = "select * from $ndatabase where " . ii_cfname('lng') . "='$slng'";
  if ($search_field == 'topic') $tsqlstr .= " and " . ii_cfname('topic') . " like '%" . $search_keyword . "%'";
  if ($search_field == 'good') $tsqlstr .= " and " . ii_cfname('good') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'hidden') $tsqlstr .= " and " . ii_cfname('hidden') . "=" . ii_get_num($search_keyword);
  if ($search_field == 'id') $tsqlstr .= " and $nidfield=" . ii_get_num($search_keyword);
  if (!ii_isnull($ttutorial_id) && $ttutorial_id != 0) $tsqlstr .= " and " . ii_cfname('tutorial_id') . "=" . $ttutorial_id;
  if (!ii_isnull($tchapter_id) && $tchapter_id != 0) $tsqlstr .= " and " . ii_cfname('chapter_id') . "=" . $tchapter_id;
  $tsqlstr .= " order by " . ii_cfname('time') . " desc";
  $tcp = new cc_cutepage;
  $tcp -> id = $nidfield;
  $tcp -> sqlstr = $tsqlstr;
  $tcp -> offset = $toffset;
  $tcp -> pagesize = $npagesize;
  $tcp -> rslimit = $nlisttopx;
  $tcp -> init();
  $trsary = $tcp -> get_rs_array();
  $font_disabled = ii_itake('global.tpl_config.font_disabled', 'tpl');
  $postfix_good = ii_ireplace('global.tpl_config.postfix_good', 'tpl');
  if (!(ii_isnull($search_keyword)) && $search_field == 'topic') $font_red = ii_itake('global.tpl_config.font_red', 'tpl');
  if (is_array($trsary))
  {
    foreach($trsary as $trs)
    {
      $ttopic = ii_htmlencode($trs[ii_cfname('topic')]);
      if (isset($font_red))
      {
        $font_red = str_replace('{$explain}', $search_keyword, $font_red);
        $ttopic = str_replace($search_keyword, $font_red, $ttopic);
      }
      if ($trs[ii_cfname('hidden')] == 1) $ttopic = str_replace('{$explain}', $ttopic, $font_disabled);
      if ($trs[ii_cfname('good')] == 1) $ttopic .= $postfix_good;
      global $variable;
      $nurltype = $variable[ii_cvgenre($ngenre) . '.nurltype'];
      $ncreatefolder = $variable[ii_cvgenre($ngenre) . '.ncreatefolder'];
      $ncreatefiletype = $variable[ii_cvgenre($ngenre) . '.ncreatefiletype'];
      $turl = '/'.$ngenre.'/'.ii_iurl('detail',$trs[$nidfield], $nurltype, 'folder=' . $ncreatefolder . ';filetype=' . $ncreatefiletype);
      $tmptstr = str_replace('{$topic}', $ttopic, $tmpastr);
      $tmptstr = str_replace('{$topicstr}', ii_encode_scripts(ii_htmlencode($trs[ii_cfname('topic')])), $tmptstr);
      $tmptstr = str_replace('{$url}', $turl, $tmptstr);//后台快捷访问专用,不可删除
      $tmptstr = str_replace('{$tutorial_id}', ii_get_num($trs[ii_cfname('tutorial_id')],'0'), $tmptstr);
      $tmptstr = str_replace('{$tutorial}', mm_get_field('tutorial',ii_get_lrstr($trs[ii_cfname('tutorial_id')], ',', 'left'),'topic'), $tmptstr);
      $tmptstr = str_replace('{$chapter_id}', ii_get_num($trs[ii_cfname('chapter_id')],'0'), $tmptstr);
      if(ii_get_num($trs[ii_cfname('chapter_id')],'0') != 0) $tmptstr = str_replace('{$chapter}', mm_get_field('tutorial/chapter',ii_get_lrstr($trs[ii_cfname('chapter_id')], ',', 'left'),'topic'), $tmptstr);
      else $tmptstr = str_replace('{$chapter}', ii_itake('manage.nochapter','lng'), $tmptstr);
      $tmptstr = str_replace('{$time}', ii_get_date($trs[ii_cfname('time')]), $tmptstr);
      $tmptstr = str_replace('{$id}', ii_get_num($trs[$nidfield]), $tmptstr);
      $tmprstr .= $tmptstr;
    }
  }
  $tmpstr = str_replace('{$cpagestr}', $tcp -> get_pagenum(), $tmpstr);
  $tmpstr = str_replace('{$tutorial_id}', $ttutorial_id, $tmpstr);
  $tmpstr = str_replace(WDJA_CINFO, $tmprstr, $tmpstr);
  $tmpstr = ii_creplace($tmpstr);
  return $tmpstr;
}

function wdja_cms_admin_manage_batch_shift()
{
  $tmpstr = ii_ireplace('manage.batch_shift', 'tpl');
  return $tmpstr;
}

function wdja_cms_admin_manage_batch_delete()
{
  $tmpstr = ii_ireplace('manage.batch_delete', 'tpl');
  return $tmpstr;
}

function wdja_cms_admin_manage_displace()
{
  switch($_GET['mtype'])
  {
    case 'batch_shift':
      return wdja_cms_admin_manage_batch_shift();
      break;
    case 'batch_delete':
      return wdja_cms_admin_manage_batch_delete();
      break;
    default:
      return wdja_cms_admin_manage_batch_shift();
      break;
  }
}

function wdja_cms_admin_manage()
{
  switch($_GET['type'])
  {
    case 'check_topic':
      return wdja_cms_interface_check_topic();
      break;
    case 'add':
      return wdja_cms_admin_manage_add();
      break;
    case 'edit':
      return wdja_cms_admin_manage_edit();
      break;
    case 'list':
      return wdja_cms_admin_manage_list();
      break;
    case 'displace':
      return wdja_cms_admin_manage_displace();
      break;
    case 'upload':
      uu_upload_files_html('upload_html');
      break;
    default:
      return wdja_cms_admin_manage_list();
      break;
  }
}
//****************************************************
// WDJA CMS Power by wdja.net
// Email: admin@wdja.net
// Web: http://www.wdja.net/
//****************************************************
?>