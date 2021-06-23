// Add new data row effect and the delete row effect:
let btn_add = document.querySelector("#btn-add");
let btn_delete = document.querySelectorAll(".btn-delete");
btn_add.addEventListener("click", function(e) {
  let table   = document.querySelector("table");
  let new_row = table.insertRow();

  let cells = ["group_label[]", "sample_label[]", "batch[]", "r1_url[]", "r1_md5[]", "r2_url[]", "r2_md5[]"];

  cells.forEach((item, i) => {
    let new_cell  = new_row.insertCell();
    let new_input = document.createElement("input");
    new_input.type = "text";
    if (item === "batch[]") {
      new_input.type = "number";
      new_input.step = 1;
      new_input.min  = 1;
      new_input.value = 1;
    }
    new_input.name = item;
    new_input.required = true;
    if ((item === "r1_md5[]") || (item === "r2_md5[]")) {
      new_input.required = false;
    }
    new_cell.appendChild(new_input);
  });

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

  console.log("new row added");
})
