<?php
//****************************************************
// WDJA CMS Power by wdja.net
// Email: admin@wdja.net
// Web: http://www.wdja.net/
//****************************************************
function mm_get_vuser($vid,$nid)
{
  //获取虚拟用户名
  global $conn, $ngenre, $variable, $nurlpre, $nlng;
  $tvuser = mm_get_field($ngenre,$nid,'vuser');
  $tgenre = 'expansion/vuser';
  $tdatabase = $variable[ii_cvgenre($tgenre) . '.ndatabase'];
  $tidfield = $variable[ii_cvgenre($tgenre) . '.nidfield'];
  $tfpre = $variable[ii_cvgenre($tgenre) . '.nfpre'];
  $trsPre = ii_itake('global.' . $tgenre . ':manage.upload_user', 'lng');
  $trsNext = ii_itake('global.' . $tgenre . ':manage.tips_user', 'lng');
  $tsqlstr = "select " . ii_cfnames($tfpre, 'topic') . " from $tdatabase where $tidfield = $vid";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  if (is_array($trs) && $tvuser == 1) return $trsPre.$trs[ii_cfnames($tfpre, 'topic')].$trsNext;
}

function mm_get_rand_vuser()
{
  //随机获取虚拟用户ID
  global $conn, $variable, $nurlpre, $nlng;
  $ngenre = 'expansion/vuser';
  $ndatabase = $variable[ii_cvgenre($ngenre) . '.ndatabase'];
  $nidfield = $variable[ii_cvgenre($ngenre) . '.nidfield'];
  $nfpre = $variable[ii_cvgenre($ngenre) . '.nfpre'];
  $tsqlstr = "select $nidfield from $ndatabase order by rand() desc";
  $trs = ii_conn_query($tsqlstr, $conn);
  $trs = ii_conn_fetch_array($trs);
  return $trs[$nidfield];
}
?>
