<?php
  session_start();
  // Create study folder:
  //// Prepare variables:
  $permittedChars = '0123456789abcdefghijklmnopqrstuvwxyz';
  $uniqueChars = substr(str_shuffle($permittedChars), 0, 20);
  $timeCurr = date("h.i.s-m.d.Y");
  $hpcPrefix = "/home/kh45w/umw_mccb/Kai/hosts/scRNAseqPreprocessing/"; // Need to mount this to scRNAseqPreprocessing/res/ folder.
  $webPrefix = "/var/www/html/scRNAseqPreprocessing/res/";

  // $webPrefix = "/Users/kaihu/Projects/webCRISPRseek/webCRISPRseek_dev/res/";
  $study_name = preg_replace('/[^a-zA-Z0-9_.]/', '_', trim($_POST["study-name"]));
  $studyFolder = "study_".$study_name."_".$timeCurr."_".$uniqueChars;
  $scriptFolder = $webPrefix.$studyFolder."/script/";
  $resultsFolder = $webPrefix.$studyFolder."/results/";
  $fastqFolder = $webPrefix.$studyFolder."/fastq/";

  mkdir($webPrefix.$studyFolder, 0777);
  chmod($webPrefix.$studyFolder, 0777);
  mkdir($scriptFolder, 0777);
  chmod($scriptFolder, 0777);
  mkdir($resultsFolder, 0777);
  chmod($resultsFolder, 0777);
  mkdir($fastqFolder, 0777);
  chmod($fastqFolder, 0777);
  // echo "Here is the folder made: ".$webPrefix.$studyFolder."<br>";

  // Prepare R script and execution command
  //// Prepare index.
  // echo "Here is the index.php: ".$webPrefix.$studyFolder."/index.php"."<br>";
  $indexFile = fopen($webPrefix.$studyFolder."/index.php", "w");
  $resLinkPage = "https://mccb.umassmed.edu/scRNAseqPreprocessing/res/".$studyFolder."/results/";
  // $rJobLog = shell_exec("cat ".$webPrefix.$studyFolder."/log_CRISPRseek.txt | tail -n 50");
  $indexContent = '
  <?php
    ini_set("display_errors", 1);
    ini_set("display_startup_errors", 1);
    // error_reporting(E_ALL);
    $job_status = shell_exec("cat '.$webPrefix.$studyFolder.'/log.snakemake.txt | tail -n 50");
    $job_status = preg_replace("/\n/", "<br>", $job_status);
    if (isset($_GET["uid"])) {
      if ($_GET["uid"] == "'.$uniqueChars.'") {
        if (file_exists("./script/.finished")) {
            header("Location: '.$resLinkPage.'");
            exit();
        }
        else {
          echo "Your job is still running, please check later ...<br><br>";
          echo "Below is the last 50 lines from hpc job log file:<br>";
          echo "Data uploading ...<br><br>";
          // echo "R script job started ...<br>";
          echo $job_status;
        }
      }
    } else {
      echo "<h1>Sorry, you do not have permission!</h1>";
    }
  ?>';
  fwrite($indexFile, $indexContent);
  fclose($indexFile);

  //// Prepare getData.sh script
  $getDataFile = fopen($scriptFolder."/getData.sh", "w");
  $getDataCmd  = "";
  for($i = 0; $i < count($_POST["group_label"]); $i ++) {
    $group = preg_replace('/[^a-zA-Z0-9_.]/', '_', trim($_POST["group_label"][$i]));
    $sample = preg_replace('/[^a-zA-Z0-9_.]/', '_', trim($_POST["sample_label"][$i]));
    $r1url = trim($_POST["r1_url"][$i]);
    $r2url = trim($_POST["r2_url"][$i]);
    $cmd_wget_r1 = "wget -O ".$hpcPrefix.$studyFolder."/fastq/".$sample."_L001_R1_001.fastq.gz ".$r1url."\n";
    $cmd_wget_r2 = "wget -O ".$hpcPrefix.$studyFolder."/fastq/".$sample."_L001_R2_001.fastq.gz ".$r2url."\n";
    $getDataCmd  = $getDataCmd.$cmd_wget_r1;
    $getDataCmd  = $getDataCmd.$cmd_wget_r2;
    fwrite($getDataFile, $cmd_wget_r1);
    fwrite($getDataFile, $cmd_wget_r2);
  };
  fclose($getDataFile);

  //// Prepare config file:
  // Sample names:
  $config = "";
  $config = $config."SAMPLES:\n";
  $arrTem = [];
  foreach(array_unique($_POST["sample_label"]) as $k=>$v) {
    $v = preg_replace('/[^a-zA-Z0-9_.]/', '_', trim($v));
    $val = explode("_", $v)[0];
    if (!in_array($val, $arrTem)) {
      $line = "    - ".$val."\n";
      $config = $config.$line;
      array_push($arrTem, $val);
    }
  }
  $config = $config."\n";
  // Fastq file path:
  $config = $config.'FASTQ_PATH:'."\n".'    "fastq"'."\n\n";
  // Transcriptome:
  if($_POST["genome"] == "homo_sapiens") {
    $config = $config.'TRANSCRIPTOME:'."\n".'    "/project/umw_mccb/software/refdata-cellranger-GRCh38-3.0.0"'."\n\n";
  } else if($_selectedGenome == "mus_musculus") {
    $config = $config.'TRANSCRIPTOME:'."\n".'    "/project/umw_mccb/software/refdata-cellranger-mm10-3.0.0"'."\n\n";
  } else if($_selectedGenome == "caenorhabditis_elegans") {
  }
  // Localcore:
  $config = $config.'LOCALCORES:'."\n".'    1'."\n\n";
  // Expected cell number:
  $config = $config.'CELL_NUM:'."\n".'    '.$_POST["cell-num"]."\n\n";
  // Memory:
  $config = $config.'MEM:'."\n".'    40000'."\n\n";
  $configFile = fopen($webPrefix.$studyFolder."/config.yaml", "w");
  fwrite($configFile, $config);
  fclose($configFile);
  // Prepare Snakefile
  $cmd_snakefile = "ln -sf /home/kh45w/umw_mccb/OneStopRNAseq/kai/Snakefile_cellranger/Snakefile ".$hpcPrefix.$studyFolder;

  include('Net/SSH2.php');
  define('NET_SSH2_LOGGING', 2);
  require_once("prepare_ghpcc.inc.php");
  $ssh->exec($cmd_snakefile);
  $ssh->disconnect();
  unset($ssh);
  // Prepare the submit.sh script:
  $submitFile = fopen($webPrefix.$studyFolder."/submit.sh", "w");
  $submitFileContent = "bash ".$hpcPrefix.$studyFolder."/script/getData.sh\n\n";
  $submitFileContent = $submitFileContent."source /home/kh45w/src/anaconda3/etc/profile.d/conda.sh\n";
  $submitFileContent = $submitFileContent."conda activate osr_rui\n\n";
  $submitFileContent = $submitFileContent."snakemake -p -k --jobs 999 --latency-wait 300 --cluster 'bsub -q long -o lsf.log -R 'rusage[mem={params.mem_mb}]' -n {threads} -R span[hosts=1] -W 40:00' > log.snakemake.txt 2>&1\n\n";
  $submitFileContent = $submitFileContent."mv *_cellrangerRes results\n";
  $submitFileContent = $submitFileContent."touch ./script/.finished\n";
  fwrite($submitFile, $submitFileContent);
  fclose($submitFile);
  // Prepare log file that is going to be stored on the web server:
  $logWebFile = 'Result link:\n';
  $logWebFile = $logWebFile.'https://mccb.umassmed.edu/scRNAseqPreprocessing/res/'.$studyFolder.'?uid='.$uniqueChars.'\n\n';
  $logWebFile = $logWebFile.'config.yaml:\n'.$config.'\n';
  $logWebFile = $logWebFile.'getData.sh:\n'.$getDataCmd.'\n';
  $logWebFile = $logWebFile.'submit.sh:\n'.$submitFileContent.'\n';
  // Prepare submit command:
  $cmd_submit = "bsub -q long -n 1 -W 40:00 -R rusage[mem=1000] -o ".$hpcPrefix.$studyFolder."/log.lsf.submit.txt "."'"."bash ".$hpcPrefix.$studyFolder."/submit.sh > ".$hpcPrefix.$studyFolder."/log.web.txt 2>&1'";
  $logWebFile = $logWebFile.$cmd_submit.'\n';
  $cmdLog = 'echo "'.$logWebFile.'" >> ../log/log_'.$studyFolder.'.txt';
  shell_exec($cmdLog);

  // Submit the jobs from ghpcc:
  require_once("prepare_ghpcc.inc.php");
  $ssh->exec($cmd_submit);
  $ssh->disconnect();
  unset($ssh);
 ?>

 <html>
   <head></head>
   <body>
     <div class="main">
       <h1>Your job was submitted!</h1>
       <!-- <p class="change">If you have provided your email address, you will receive an email reminder containing result link once the job is done.</p> -->
       <p class="change">Please take a note of the following link for your reference:</p>
       <a href='https://mccb.umassmed.edu/scRNAseqPreprocessing/res/<?php echo $studyFolder; ?>?uid=<?php echo $uniqueChars;?>' id="a-res">https://mccb.umassmed.edu/scRNAseqPreprocessing/res/<?php echo $studyFolder; ?>?uid=<?php echo $uniqueChars;?></a>
       <!-- $tem = "https://mccb.umassmed.edu/OneStopRNAseqRes/gsea/";
       <p><strong>'.$tem.$study_folder.'/name1</strong></p> -->
       <p class="change">Your results will be stored in our server for 3 days.</p>
     </div>
   </body>
</html>
