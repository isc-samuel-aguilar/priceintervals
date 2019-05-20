<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Price Intervals App</title>
    <link rel="stylesheet" type="text/css" href="../views/css/main.css">

</head>
<body>
<div id="div_inputs">
    <h3>Add Interval</h3>
    <label > 
        Start Date
        <input type="date" id='date_start' class="dateWidth" required>
    </label>
    <label >
        End Date
        <input type="date" id='date_end' class="dateWidth" required>
    </label>
    <span>
        <label>Price $
            <input id="price"  type="number" class="numberWidth"              
                title="Price"
                placeholder="0.00" min="0" value="0" step="0.01"  
                pattern="^\d+(?:\.\d{1,2})?$" 
                onblur="this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'"
                onfocusout="this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'"
                required 
            >
        </label>
    </span>
    <label>
        <button id="btn_insert" onclick="insertHtmlRow();" > (+) Add </button> (Fill all fields to enable ADD button).
    </label>
    <hr>
    <h3>Edit Interval</h3>
    <label > 
        id
        <select id="update_select" onchange="prepareEdit(this.value, this)">
            <option  value="">Select</option>
        </select>        
    </label>    
    <label > 
        Start Date
        <input type="date" id='update_date_start' class="dateWidth" required>
    </label>
    <label >
        End Date
        <input type="date" id='update_date_end' class="dateWidth" required>
    </label>    
    <span>
        <label>Price $            
            <input id="update_price"  type="number"  class = "numberWidth"                
                title="Price"
                placeholder="0.00" min="0" value="0" step="0.01"  
                pattern="^\d+(?:\.\d{1,2})?$" 
                onblur="this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'"
                onfocusout="this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'"
                required 
            >
        </label>
    </span>
    <label>
        <button id="update_btn" onclick="updateHtmlRow();" disabled> Update </button>
        (Fill all fields to enable UPDATE button).
    </label>

    <hr>
    
    <h3>Test Section </h3>
    <!--
    <label>
        Prepare case
        <select>
            <option value='CASE.A' selected>CASE.A</option>
            <option value='CASE.B'>CASE.B</option>
            <option value='CASE.C'>CASE.C</option>
            <option value='CASE.D'>CASE.D</option>
            <option value='CASE.E'>CASE.E</option>
            <option value='CASE.F'>CASE.F</option>
            <option value='CASE.G'>CASE.G</option>
            <option value='CASE.H'>CASE.H</option>        
        </select>
        <button class="btn-prepare-case">Prepare DB Case</button>         
        <button class="btn-prepare-case" onclick="deleteAllHtmlRows">Empty DB</button>
    </label>
    -->
    
    <label>
        <button onclick="deleteHtmlRows('*')" >Empty DataBase</button>
    </label>
    <hr>
   
</div>
<hr>
<div id="div-intervals" class="table-wrapper">
    <h3>Price Intervals view (This view apply for dates of the same month)</h3><br>
    <table class="fl-table" id="tbl-intervals">
        <thead class="head-intervals">
            <tr>
            <th>id</th>
            <th>date_start</th>
            <th>date_end</th>
            <th>price</th>
            <th>Edit</th>
            <th>Delete</th>            
            <?php 
                for($dayNum = 1; $dayNum <= 31; $dayNum++): 
                    echo "<th>$dayNum </th>";
                endfor;
            ?>
            </tr>
        </thead>
        <tbody class="rows-intervals">
        </tbody>
    </table>    
</div>
<hr>
<div id="div-js-table" class="table-wrapper">

</div>
<script src="../views/js/main.js"></script>

</body>
</html>