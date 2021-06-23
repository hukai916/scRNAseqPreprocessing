let btn_example = document.querySelector("#btn-example");
let group_label = ["WT", "WT", "R188Q", "R188Q"];
let sample_label = ["WT_S1", "WT_S2", "R188Q_S5", "R188Q_S6"];
let batch = [1, 1, 1, 1];
let r1_url = ["https://www.dropbox.com/sh/dn9iqc85evtmxik/AACkUFU3TalHCaqb_LTqUUewa/WT_S1_L001_R1_001.fastq.gz?dl=0", "https://www.dropbox.com/sh/dn9iqc85evtmxik/AAAwBGpEZPhFnVa9uEILvc0Wa/WT_S2_L001_R1_001.fastq.gz?dl=0", "https://www.dropbox.com/sh/dn9iqc85evtmxik/AABxXPWG8BVf-EADKn6_uYwTa/R188Q_S5_L001_R1_001.fastq.gz?dl=0", "https://www.dropbox.com/sh/dn9iqc85evtmxik/AACxAlWxXpK-b18AOw5Kc1Tba/R188Q_S6_L001_R1_001.fastq.gz?dl=0"];
let r1_md5 = [];
let r2_url = ["https://www.dropbox.com/sh/dn9iqc85evtmxik/AAABg8lAcLiRqxwUiPzvyhq7a/WT_S1_L001_R2_001.fastq.gz?dl=0", "https://www.dropbox.com/sh/dn9iqc85evtmxik/AACu4SIjZ8GNrN8yrTfxfhoha/WT_S2_L001_R2_001.fastq.gz?dl=0", "https://www.dropbox.com/sh/dn9iqc85evtmxik/AADGJOFiY9KyZuh7thBkmN3ia/R188Q_S5_L001_R2_001.fastq.gz?dl=0", "https://www.dropbox.com/sh/dn9iqc85evtmxik/AAC7nC_yI1N1aVsmAM0CYD-Na/R188Q_S6_L001_R2_001.fastq.gz?dl=0"];
let r2_md5 = [];

btn_example.addEventListener("click", function(e){
  // Remove current table tr:
  let trs = [...document.querySelectorAll("tr")];
  trs.forEach((item, i) => {
    if (item.querySelector("td")) {
      item.remove();
    }
  });
  // Add example data:
  let table   = document.querySelector("table");
  for (let i = 0; i < group_label.length; i++) {
    let new_row = table.insertRow();
    let cells = ["group_label[]", "sample_label[]", "batch[]", "r1_url[]", "r1_md5[]", "r2_url[]", "r2_md5[]"];
    cells.forEach((item, j) => {
      let new_cell  = new_row.insertCell();
      let new_input = document.createElement("input");
      new_input.type = "text";
      if (item === "batch[]") {
        new_input.type = "number";
        new_input.step = 1;
        new_input.min  = 1;
        new_input.value = batch[i];
      }
      new_input.name = item;
      new_input.required = true;
      if ((item === "r1_md5[]") || (item === "r2_md5[]")) {
        new_input.required = false;
      }
      if (item === "group_label[]") {
        new_input.value = group_label[i];
      } else if (item === "sample_label[]") {
        new_input.value = sample_label[i];
      } else if (item === "r1_url[]") {
        new_input.value = r1_url[i];
      } else if (item === "r2_url[]") {
        console.log("r2");
        console.log(r2_url[i]);
        new_input.value = r2_url[i];
      }
      new_cell.appendChild(new_input);
    })
    let new_cell = new_row.insertCell();
    let new_btn = document.createElement("button");
    new_btn.className = "btn-delete";
    new_btn.type = "button";
    new_btn.innerText = "DELETE";
    new_cell.appendChild(new_btn);
    btn_delete = [...document.querySelectorAll(".btn-delete")];
    // Add delete row effect:
    btn_delete.forEach((item, i) => {
      item.addEventListener("click", function(e) {
        console.log("clicked detel");
        let tr = $(this).closest("tr");
        console.log("length: " + document.querySelectorAll("tr").length);
        if (document.querySelectorAll("tr").length > 2) {
          tr.remove(); // If only one row left, do not remove.
        }
      })
    });
  }


})
