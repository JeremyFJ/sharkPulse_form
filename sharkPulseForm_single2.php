<?php
require_once('../pulseMonitor/postgreConfig.php');
$dat = "select * from instagram where ((sd_species='Galeocerdo cuvier') 
        or (sd_species='Rhincodon typus')) and (repost='no') 
        and (longitude is not null) and (validated='f') 
        order by random() limit 1;";
$dat = pg_fetch_row(pg_query($dbconn, $dat));
$path = 'shark/';
$img = $dat[8];
$jpg = '.jpg';

/* value="<?php echo $dat[10];?>" */
?>

<html>

<head>
  

  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCUGSZplN71s1ysCfwZoavTxn86CNoXr4E&libraries=places"></script>
  <script type="text/javascript" src="geotag.js"></script>
  <script type="text/javascript" src="species_auto.js"></script>
  <link rel="stylesheet" type = "text/css" href="styles.css">

<style>

.switch-field {
display: flex;
margin-bottom: 10px;
overflow: hidden;
}

.center {
display: flex;
justify-content: center;
align-items: center;
}

.switch-field input {
position: absolute !important;
clip: rect(0, 0, 0, 0);
height: 1px;
width: 1px;
border: 0;
overflow: hidden;
}

.switch-field label {
background-color: #e4e4e4;
color: rgba(0, 0, 0, 0.6);
font-size: 14px;
line-height: 1;
text-align: center;
padding: 8px 16px;
margin-right: -1px;
border: 1px solid rgba(0, 0, 0, 0.2);
box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
transition: all 0.1s ease-in-out;
}

.switch-field label:hover {
cursor: pointer;
}

.switch-field input:checked + label {
/* 	background-color: #a5dc86; */
background-color: rgb(102, 187, 102);
box-shadow: none;
}

.switch-field label:first-of-type {
border-radius: 4px 0 0 4px;
}

.switch-field label:last-of-type {
border-radius: 0 4px 4px 0;
}

.form-element {
position: relative;
margin-top: 36px;
margin-bottom: 36px;
}

.form-element-bar {
position: relative;
height: 1px;
background: #999;
display: block;
}

.form-element-bar::after {
content: "";
position: absolute;
bottom: 0;
left: 0;
right: 0;
background: #337ab7;
height: 2px;
display: block;
transform: rotateY(90deg);
transition: transform 0.28s ease;
will-change: transform;
}

.form-element-label {
position: absolute;
top: 12px;
line-height: 24px;
pointer-events: none;
padding-left: 2px;
z-index: 1;
font-size: 16px;
font-weight: normal;
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
margin: 0;
color: #a6a6a6;
transform: translateY(-50%);
transform-origin: left center;
transition: transform 0.28s ease, color 0.28s linear, opacity 0.28s linear;
will-change: transform, color, opacity;
}

.form-element-field {
outline: none;
height: 24px;
display: block;
background: none;
padding: 2px 2px 1px;
font-size: 16px;
border: 0 solid transparent;
line-height: 1.5;
width: 100%;
color: #333;
box-shadow: none;
opacity: 0.001;
transition: opacity 0.28s ease;
will-change: opacity;
}

.icon {
    background: url('instaicon.svg');
    height: 48px;
    width: 48px;
    display: block;
    /* Other styles here */
}

.form-element-field:-ms-input-placeholder {
color: #a6a6a6;
transform: scale(0.9);
transform-origin: left top;
}

.form-element-field::placeholder {
color: #a6a6a6;
transform: scale(0.9);
transform-origin: left top;
}

.form-element-field:focus ~ .form-element-bar::after {
transform: rotateY(0deg);
}

.form-element-field:focus ~ .form-element-label {
/*   color: #337ab7; */
color: green;
}

.form-element-field.-hasvalue,
.form-element-field:focus {
opacity: 1;
}

.form-element-field.-hasvalue ~ .form-element-label,
.form-element-field:focus ~ .form-element-label {
transform: translateY(-100%) translateY(-0.5em) translateY(-2px) scale(0.9);
cursor: pointer;
pointer-events: auto;
}

/* This is just for CodePen. */

.form {
background-color: #15172b;
max-width: 600px;
font-family: "Lucida Grande", Tahoma, Verdana, sans-serif;
font-weight: normal;
line-height: 1.625;
margin: 8px auto;
padding: 16px;
font-size: 14px;
}

h2 {
font-size: 18px;
margin-bottom: 5px;
font-weight: bold;
}

img {
z-index: 10;
}

* {
  box-sizing: border-box;
}

.row {
  display: flex;
}

/* Create two equal columns that sits next to each other */
.column {
  flex: 50%;
  padding: 10px;
}
      </style>

  <script> 
            function init() {
              var sessionToken = new google.maps.places.AutocompleteSessionToken();
                var input = document.getElementById('edit-field-address-value');
                var autocomplete = new google.maps.places.Autocomplete(input);
            }

            google.maps.event.addDomListener(window, 'load', init);
        </script>

  <script>

          if (aLat && aLong) {
            // convert from deg/min/sec to 1imal for Google
            var fLat = (aLat[0] + aLat[1] / 60 + aLat[2] / 3600) * (strLatRef == "N" ? 1 : -1);
            var fLong = (aLong[0] + aLong[1] / 60 + aLong[2] / 3600) * (strLongRef == "W" ? -1 : 1);

            // setting the text field in the form
            $('#edit-field-lat-0-value').val(fLat);
            $('#edit-field-lng-0-value').val(fLong);

            // moving the marker
            moveMarker(fLat, fLong);
            

          // date info
          if (aDate) {
            $('#formDate').val(aDate);
          }
        

      }
    </script>


</head>

<body>
<div class="row">
<div class="column">
<div class="column">
<h1>Tigers and Whales<h1> <i class='icon'></i>
<h3><?php echo '#'.$dat[17] ?><h3>
<a target="_blank" href=<?php echo $dat[6]?>>
<img src=<?php echo $path.$img.$jpg ?> width="400"/>
</a><br>
<?php echo 'Date: '.$dat[0] ?>
</div>

<br><b>Suggested Species:</b><br>
<?php echo $dat[10]; echo '  '.(round($dat[11], 3)*100).'%'; ?><br><br>
<a href="http://sp2.cs.vt.edu/identification-guide" onclick="window.open(this.href, 'windowName', 'width=1000, height=700, left=24, top=24, scrollbars, resizable'); return false;">Identification guide</a>
  
<form name="sharkform" action="http://sp2.cs.vt.edu/validationMonitor/action_page_insta.php" method="POST">

  </div>
          <div class="column">             
                <div class="row">
                <div class="column">
                    <h2>Is this a shark?</h2>
                    <div class="switch-field">
                        <input type="radio" id="radio-one" name="radioshark" value="yes" required/>
                        <label for="radio-one">Yes</label>
                        <input type="radio" id="radio-two" name="radioshark" value="no"/>
                        <label for="radio-two">No</label>
                    </div>
                </div>
                <div class="column">
                    <h2>In an aquarium?</h2>
                    <div class="switch-field">
                        <input type="radio" id="radio-three" name="radioaq" value="yes" />
                        <label for="radio-three">Yes</label>
                        <input type="radio" id="radio-four" name="radioaq" value="no"/>
                        <label for="radio-four">No</label>
                    </div>
                </div>
            </div>

    <p>
        If this is not the correct location of the sighting, use the search tool or drag the marker: <br>
        <input type="text" size="60" placeholder="Enter Location" name="address" id="edit-field-address-value" />
        <button type="button" onClick="codeAddress(address.value); return false">Click to Search</button>
      </p>

      <div id="map_canvas" style="width: 700px; height: 350px"></div>

      <label for="edit-field-lat-0-value">Latitude: </label>
      <input type="text" maxlength="13" name="formLatitude" value=<?php echo $dat[4] ?> id="edit-field-lat-0-value" size="15" class="form-text number" />

      <label for="edit-field-lng-0-value">Longitude: </label>
      <input type="text" maxlength="13" name="formLongitude" value=<?php echo $dat[5] ?> id="edit-field-lng-0-value" size="15" class="form-text required number" />
      <button type='button' onClick='codeCoords()'>Search</button>
      <br>
      <div style="margin-top: 15px;font-size:14pt;">
      Common Name<br><input id="commonname" size="50" type="text" name="common"/>
                <label for="commonname"></label>
                
            </div>
        <div style="margin-top: 15px;font-size:14pt;">
        Species Name<br><input id="speciesname" size="50" type="text" name="species"/>
                <label for="speciesname"></label>
            </div>
        <div style="margin-top: 15px;font-size:14pt;">
        Comments<br><input id="comments" size="75" placeholder="Please leave any comments you may have" type="text" name="comment"/>
                <label for="comments"></label>
            </div>

            <input type="hidden" value=<?php echo $dat[19];?> name="id">
            <input type="hidden" value=<?php echo $path.$img.$jpg ?> name="img_name">
      <br><br>
      <input type="submit" onClick="submit" value="Submit" />

    </form>


</body>

</html>
