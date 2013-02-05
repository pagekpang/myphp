<?php
// import sql
require '../config.php';
// import mibao util
require_once '../includes/dz_mibaocard.php';

//echo dz_mibao_util::rand_cardid();
$row = dz_mibao_util::rand_row();
dz_mibao_util::show(dz_mibao_util::rand_cardid(), $row, dz_mibao_util::rand_code($row));
