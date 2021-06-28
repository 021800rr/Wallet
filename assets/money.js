let cells_amount = Array.prototype.slice.call(document.querySelectorAll(".row_money_amount"));

cells_amount.forEach(function(cell_amount){
    if(cell_amount.textContent > 0) {
        cell_amount.className += (cell_amount.className ? " " : "")+"text-success fw-bold";
    }
});

let cells_backup_amount = Array.prototype.slice.call(document.querySelectorAll(".row_money_backup_amount"));

cells_backup_amount.forEach(function(cell_backup_amount){
    if(cell_backup_amount.textContent < 0) {
        cell_backup_amount.className += (cell_backup_amount.className ? " " : "")+"text-danger fw-bold";
    }
});

let cells = Array.prototype.slice.call(document.querySelectorAll(".row_money, .row_money_amount, .row_money_backup_amount"));

cells.forEach(function(cell){
    cell.textContent = (+cell.textContent).toLocaleString('pl-PL', { style: 'currency', currency: 'PLN' });
});
