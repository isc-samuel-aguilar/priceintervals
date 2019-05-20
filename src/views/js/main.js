/* Global Vars (workarround wihout use frameworks) *********************************************************** */


//Global var that contains the db Information and help to update the table
var dbIntervals = new Map(); //[ dbId = {id, date_start, date_end, price} ]

// Function to update
/**
 *
 * @param array data list of intervals
 * @param bool refresh
 */
function updateIntervalMap(data, refresh = true){
    dbIntervals = new Map(); 
    
    if (refresh){      
      cleanSelect();
      cleanHtmlTable();
    } 

    for (const [key, interval] of Object.entries(data)) {        
      dbIntervals.set(parseInt(interval.id), interval);
      if (refresh) {
        addTablelRow(interval.id, interval.date_start, interval.date_end, interval.price);
        addOption(interval.id);
      }      
    }    
}

var globalNote1 = "Note: The stlyle/type of this message can be replaced by bootstrap or angular message";

/* DOM Functions *********************************************************** */

function addOption(optVal, optText , idSelect){
  idSelect = idSelect || 'update_select';
  optText = optText || optVal;
 
  let sel = document.getElementById(idSelect);  
  let opt = document.createElement('option');
  opt.appendChild( document.createTextNode(optText) );
  opt.value = optVal; 
  sel.appendChild(opt);  
}

function addTablelRow(id, date_start, date_end, price){
  let tabLines = document.querySelector("#tbl-intervals").querySelector('.rows-intervals');
  let tabLinesRow = tabLines.insertRow(tabLines.rows.length);

  //Index of the column number
  let colNum = 0;

  let col1 = tabLinesRow.insertCell(colNum++); col1.innerHTML=id;
  let col2 = tabLinesRow.insertCell(colNum++); col2.innerHTML=date_start;
  let col3 = tabLinesRow.insertCell(colNum++); col3.innerHTML=date_end;
  let col4 = tabLinesRow.insertCell(colNum++); col4.innerHTML=price;  

  //Add Edit Button
  let colEdit   = tabLinesRow.insertCell(colNum++); 
  colEdit.innerHTML=`<td><button onclick='prepareEdit(${id})'>Edit</button></td>`;    
  //Add Delete Button
  let colDelete = tabLinesRow.insertCell(colNum++); 
  colDelete.innerHTML=`<td><button onclick='deleteHtmlRows(${id}, this)'>Delete</button></td>`;
    

  let colDays = {};
  //Loop to update the days
  for(let dayNum = 1; dayNum <= 31; dayNum++){
    colDays[ 'day_' + dayNum ] = tabLinesRow.insertCell(colNum++);   

    if( 
      (new Date(date_start.replace(/-/g,'/')).getDate()) <= dayNum && 
        dayNum <= (new Date(date_end.replace(/-/g,'/')).getDate()) 
    ){
      colDays[ 'day_'+dayNum ].outerHTML = `<td class='fillBackground'>${price}</td>`;
    } else {
      colDays[ 'day_'+dayNum ].outerHTML = `<td>&nbsp; - </td>`; 
    }    
  }

  //return result;
}

function cleanSelect(idSelect){
    idSelect = idSelect || 'update_select' ;
    
    document.querySelector('#update_select').value = "";
    document.querySelector('#update_date_start').value = "";
    document.querySelector('#update_date_end').value = "";
    document.querySelector('#update_price').value = "";
    document.querySelector('#update_btn').disabled = true;
        
    let update_select = document.querySelector('#'+idSelect);
    for(let opNum = update_select.options.length - 1 ; opNum > 0 ; opNum--) {
        update_select.remove(opNum);
    }  
}

function cleanHtmlTable(tableId){
    tableId = tableId||'tbl-intervals';
    var Parent = document.querySelector('#'+tableId).querySelector('tbody');
    while(Parent.hasChildNodes()) {
       Parent.removeChild(Parent.firstChild);
    }
    document.querySelector('#date_start').value = ""; // '2019-02-01';
    document.querySelector('#date_end').value = ""; //'2019-02-01';
    document.querySelector('#price').value = ""; //"0.00";    
}

function prepareEdit(dbId, selectId){
  selectId = selectId || 'update_select';
  let interval = dbIntervals.get(parseInt(dbId));
  if(interval === undefined){
    document.querySelector('#update_select').value = "";
    document.querySelector('#update_date_start').value = "";
    document.querySelector('#update_date_end').value = "";
    document.querySelector('#update_price').value = "";
    document.querySelector('#update_btn').disabled = true;
    document.querySelector('#update_select').focus();

  } else {
    document.querySelector('#update_select').value = dbId;
    document.querySelector('#update_date_start').value = interval.date_start;
    document.querySelector('#update_date_end').value = interval.date_end;
    document.querySelector('#update_price').value = interval.price;
    document.querySelector('#update_btn').disabled = false;
    document.querySelector('#update_select').focus();    
  }
}

/* Call DB Functions  *********************************************************** */

function insertHtmlRow(tableId){  
  if ( 
    document.querySelector('#date_start').value === "" || 
    document.querySelector('#date_end').value === "" || 
    document.querySelector('#date_price').value === "" 
  ){
    alert("The following fields are required:" + "\n  * Start Date \n  * End Date \n  * Price" +
      "\n\n Note: " + globalNote1
    );
  } else {
    tableId = tableId || 'tbl-intervals';
    let dataPromise = new Promise( (resolve, reject) => resolve(insertDBInterval()) );
    dataPromise.then(data => updateIntervalMap(data))
    .catch(
        error => console.log('error')
    );
    
  }
}

function updateHtmlRow(tableId){  
  tableId = tableId || 'tbl-intervals';
  
  if (
    document.querySelector('#update_select').value === "" || 
    document.querySelector('#update_date_start').value === "" || 
    document.querySelector('#update_date_end').value === "" || 
    document.querySelector('#update_date_price').value === "" 
  ){
    alert("The following fields are required:" + "\n  * id  \n  * Start Date \n  * End Date \n  * Price" +
      "\n\n Note: " + globalNote1
    );
  } else {
    let dataPromise = new Promise( (resolve, reject) => resolve(updateDBInerval()) );
    dataPromise.then((data) => updateIntervalMap(data) )
    .catch((error) => console.log('error') );    
  }


}


/**
 * The row of the pressed button will be removed
 * @param int dbId id of the database of the interval
 * @param obj button of delete
 * @returns {boolean}
 */
function deleteHtmlRows(dbId, obj ){
    let tableId = 'tbl-result';
    let table = document.querySelector('#' + tableId);

    if(dbId !== undefined ){
      let dataPromise = new Promise( (resolve, reject) => resolve(deleteDBRow(dbId)) );
      dataPromise.then(function (data){
        if(obj !== undefined) {
          updateIntervalMap(data, false);
          obj.parentElement.parentElement.remove();          
        } else {
          updateIntervalMap(data)
        }
      })
      .catch( (error) => console.log(error) );
    } else {
      return false;
    }
}

/**
 *
 * @param string tableId id of the table to refresh
 * @returns {Promise<void>}
 */
async function refreshHtmlTable (tableId){
  tableId = tableId || 'tbl-intervals';  
  let dataPromise = new Promise(
    function (resolve, reject){      
      resolve( 
        getDBIntervals(tableId)
      );
    }
  );
  dataPromise.then( data => updateIntervalMap(data) )
  .catch( error => console.log(error));
}


/* Definition of DB Functions *********************************************************** */

async function insertDBInterval(){    
    let url = window.location.pathname+'/../priceinterval/';
    let data = {
        'date_start': document.querySelector('#date_start').value,
        'date_end'  : document.querySelector('#date_end').value,
        'price'     : document.querySelector('#price').value
    };
    
    return fetch(url, {
      method: 'POST',
      body: JSON.stringify(data), 
      headers:{'Content-Type': 'application/json'}
    })
    .then(response => response.json() )
    .then(data => data)
    .catch(error =>{
      console.error('Error:', error) ;
      alert('Request not completed, contact your administrator' + '\n\n' + globalNote1);
      refreshHtmlTable();    
    });    
}

async function deleteDBRow(id){    
    let url = window.location.pathname+'/../priceinterval/'+id;
    return fetch(url, {
      method: 'DELETE',
      headers:{ 'Content-Type': 'application/json'}
    })
    .then( response => response.json()  )
    .then(data => data)
    .catch(error =>{
      console.error('Error:', error) ;
      alert('Request not completed, contact your administrator' + '\n\n' + globalNote1);
      refreshHtmlTable();    
    });    
}

async function updateDBInerval(){
    let url = window.location.pathname+'/../priceinterval/';
    let data = {
        'id'        : document.querySelector('#update_select').value,
        'date_start': document.querySelector('#update_date_start').value,
        'date_end'  : document.querySelector('#update_date_end').value,
        'price'     : document.querySelector('#update_price').value
    };
    
    return fetch(url, {
      method: 'PUT', 
      body: JSON.stringify(data), 
      headers:{'Content-Type': 'application/json'}
    })
    .then(response => response.json() )
    .then(data => data)
    .catch(error =>{
      console.error('Error:', error) ;
      alert('Request not completed, contact your administrator' + '\n\n' + globalNote1);
      refreshHtmlTable();    
    });    
}

async function getDBIntervals() {    
    let url = window.location.pathname+'/../priceinterval/';    
    let result = [];
    
    return fetch(url, {
      method: 'GET', 
      headers:{
        'Content-Type': 'application/json'
      }
    })
    .then(response => response.json())
    .then(function (data){      
      updateIntervalMap(data);;
      return data;
    })
    .catch(error =>{
      console.error('Error:', error) ;
      alert('Request not completed, contact your administrator' + '\n\n' + globalNote1);
      refreshHtmlTable();    
    });    
}

/* Under Develop *********************************************************** */

refreshHtmlTable();
document.querySelector('#date_start').focus();
