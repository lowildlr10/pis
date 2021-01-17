// JavaScript Document
     function addRow()
     {

		 // grab the element, i.e. the table your editing, in this we're calling it 
          // 'mySampleTable' and it is reffered to in the table further down on the page 
          // with a unique of id of you guessed it, 'mySampleTable'
          var tbl = document.getElementById('tblInn');
          // grab how many rows are in the table
          var lastRow = tbl.rows.length;
		  //var lastRow = document.frmPR.txtItemCount.value;
          // if there's no header row in the table (there should be, code at least one 
          //manually!), then iteration = lastRow + 1
          var iteration = lastRow;
          // creates a new row
          var row = tbl.insertRow(lastRow);

          // left cell
          // insert a cell
          
		  var cellQty = row.insertCell(0);
          var e0 = document.createElement('input');
          e0.type = 'text';
		  e0.name = 'txtQty' + iteration;
		  e0.id = 'txtQty' + iteration;
		  e0.size = 5;
		  e0.onkeypress = checkIt;
		  cellQty.appendChild(e0);
		  
		 var cellRightSel = row.insertCell(1);
          // create another element, this time a select box
          var sel = document.createElement('select');
          // name it, once again with an iteration (selRow8 using the example above)
          sel.name = 'selUnit' + iteration;
          // crates options in an array
          // the Option() function takes the first parameter of what is being displayed
          // from within the drop down, and the second parameter of the value it is carrying over
		  sel.options[0] = new Option(' ', ' ');
		  sel.options[1] = new Option('Bag', 'Bag');
          sel.options[2] = new Option('Bar', 'Bar');
          sel.options[3] = new Option('Book', 'Book');
		  sel.options[4] = new Option('Bottle', 'Bottle');
		  sel.options[5] = new Option('Box', 'Box');
		  sel.options[6] = new Option('Bundle', 'Bundle');
		  sel.options[7] = new Option('Can', 'Can');
		  sel.options[8] = new Option('Cartoon', 'Cartoon');
		  sel.options[9] = new Option('J.O.', 'J.O.');
		  sel.options[10] = new Option('Kilo', 'Kilo');
		  sel.options[11] = new Option('Pack', 'Pack');
		  sel.options[12] = new Option('Pad', 'Pad');
		  sel.options[13] = new Option('Pair', 'Pair');
		  sel.options[14] = new Option('Piece', 'Piece');
		  sel.options[15] = new Option('Ream', 'Ream');
		  sel.options[16] = new Option('Roll', 'Roll');
		  sel.options[17] = new Option('Set', 'Set');
		  sel.options[18] = new Option('Tube', 'Tube');
		  sel.options[19] = new Option('Unit', 'Unit');
          // append our new element containing new options to our new cell
          cellRightSel.appendChild(sel);
		  
		 var cellItem = row.insertCell(2);
          var e1 = document.createElement('textarea');
          //e1.type = 'text';
		  e1.name = 'txtDesc' + iteration;
		  e1.id = 'txtDesc' + iteration;
		  e1.rows = 2;
		  e1.cols=30;
		  cellItem.appendChild(e1);
		  
		  var cellStock = row.insertCell(3);
          var e2 = document.createElement('input');
          e2.type = 'text';
		  e2.name = 'txtStockNo' + iteration;
		  e2.id = 'txtUnits' + iteration;
		  e2.size = 5;
		  e2.onkeypress = checkIt;
		  cellStock.appendChild(e2);
		  
		  var cellEUC = row.insertCell(4);
          var e3 = document.createElement('input');
          e3.type = 'text';
		  e3.name = 'txtEUC' + iteration;
		  e3.id = 'txtEUC' + iteration;
		  e3.size = 10;
		  e3.onkeypress=checkIt;
		  cellEUC.appendChild(e3);
		  
		  var cellEC = row.insertCell(5);
          var e4 = document.createElement('input');
          e4.type = 'text';
		  e4.name = 'txtEC' + iteration;
		  e4.id = 'txtEC' + iteration;
		  e4.size = 10;
		  e4.onkeypress=checkIt;
		  e4.onchange=computeCost;
		  e4.disabled = 'disabled'; 
		  cellEC.appendChild(e4);
          // our last cell!
          
		  document.frmPR.txtItemCount.value=iteration;
	 }

     function removeRow()
     {
          // grab the element again!
          var tbl = document.getElementById('tblInn');
          // grab the length!
          var lastRow = tbl.rows.length;
          // delete the last row if there is more than one row!
          if (lastRow > 2) tbl.deleteRow(lastRow - 1);
     }
