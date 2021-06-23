<?php
  $ssh = new Net_SSH2('ghpcc06.umassrc.org');
  if (!$ssh->login('kh45w', '@Guang202101')) {
    exit('ERROR: SSH login failed!');
  }
