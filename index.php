
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/master.css">
    <title>scRNAseqPreprocessing will Cell Ranger</title>
  </head>

  <body>
    <header>
        <h2>scRNAseqPreprocessing (v0.0.1)</h2>
    </header>

    <main>
      <form id="master-form" action="includes/process.inc.php" method="post">
        <div id="meta-info">
          <h3>Meta info</h3>
          <div class="meta-info study-name">
            <p>Study Name</p>
            <input type="text" name="study-name" value="Example_study" required>
          </div>

          <div class="meta-info genome">
            <p>Genome</p>
            <select name="genome">
              <option value="homo_sapiens">Homo Sapiens</option>
              <option value="mus_musculus">Mus Musculus</option>
            </select>
          </div>

          <div class="meta-info sequencing-type">
            <p>Sequencing type</p>
              <select class="" name="">
                <option value="">Paired-end</option>
                <!-- <option value=""></option> -->
              </select>
          </div>

          <div class="meta-info expected-cells">
            <p>Expected cell number</p>
            <input type="number" step=1 value=1000 name="cell-num" value="" min=0>
          </div>
        </div>

        <div id="data-links">
          <h3>Data links<br>(Please provide dropbox links.)<br>(Also note that if group label is "WT", sample label must be like "WT_S1, WT_S2, <i>etc.</i>")<br><button id="btn-example" type="button">Example</button></h3>
          <table>
            <tbody>
              <tr>
                <th>Group label</th>
                <th>Sample label</th>
                <th class="th-batch">Batch</th>
                <th>R1 URL</th>
                <th>R1 MD5</th>
                <th>R2 URL</th>
                <th>R2 MD5</th>
                <th>
                  <button id="btn-add" type="button">ADD</button>
                </th>
              </tr>

              <tr>
                <td>
                  <input type="text" name="group_label[]" required>
                </td>
                <td>
                  <input type="text" name="sample_label[]" required>
                </td>
                <td>
                  <input type="number" step=1 min=1 name="batch[]" value=1 required>
                </td>
                <td>
                  <input type="text" name="r1_url[]" required>
                </td>
                <td>
                  <input type="text" name="r1_md5[]">
                </td>
                <td>
                  <input type="text" name="r2_url[]" required>
                </td>
                <td>
                  <input type="text" name="r2_md5[]">
                </td>
                <td>
                  <button class="btn-delete" type="button" name="btn-delete">DELETE</button>
                </td>
              </tr>

            </tbody>
          </table>
        </div>

        <div id="div-submit">
          <button id="btn-submit" type="submit" name="button">Submit</button>
        </div>
      </form>
    </main>


    <footer>
      <h2>UMMS@2021</h2>
    </footer>

    <!-- Load JQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Table add/delete effect -->
    <script src="js/table.js"></script>
    <!-- Add in example data -->
    <script src="js/example.js"></script>


  </body>
</html>
