<?php

$api = "https://api.ppv.st/api/streams";

$ch = curl_init($api);

curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_FOLLOWLOCATION=>true,
    CURLOPT_SSL_VERIFYPEER=>false,
    CURLOPT_TIMEOUT=>30,
    CURLOPT_USERAGENT=>"Mozilla/5.0"
]);

$json = curl_exec($ch);

curl_close($ch);

$data = json_decode($json,true);

if(!$data || empty($data['streams'])){
    die("Unable to load streams");
}

?>
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>PPV Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{

background:#0d1117;

color:white;

padding:25px;

}

h1{

font-size:30px;

margin-bottom:20px;

}

#search{

width:100%;

padding:15px;

font-size:18px;

border:none;

border-radius:10px;

background:#161b22;

color:white;

margin-bottom:20px;

outline:none;

}

.category{

margin-top:30px;

}

.category h2{

background:#161b22;

padding:15px;

border-radius:10px;

font-size:22px;

}

.match{

display:flex;

justify-content:space-between;

align-items:center;

padding:14px;

margin-top:10px;

background:#1d232d;

border-radius:10px;

transition:.3s;

}

.match:hover{

background:#26303d;

}

.title{

font-size:18px;

font-weight:bold;

width:75%;

}

button{

background:#238636;

color:white;

border:none;

padding:10px 18px;

font-size:15px;

cursor:pointer;

border-radius:8px;

}

button:hover{

background:#2ea043;

}

.toast{

position:fixed;

bottom:20px;

right:20px;

background:#238636;

padding:15px 20px;

border-radius:8px;

display:none;

}

.live{

color:#00ff88;

font-weight:bold;

margin-left:8px;

}

.upcoming{

color:#ffb347;

font-weight:bold;

margin-left:8px;

}

</style>

</head>

<body>

<h1>PPV Embed Dashboard</h1>

<input
id="search"
placeholder="Search Match..."
type="text">

<div id="content">

<?php

foreach($data['streams'] as $cat){

echo "<div class='category'>";

echo "<h2>".$cat['category']."</h2>";

foreach($cat['streams'] as $stream){

$title=$stream['name'];

$link = strtok($stream['iframe'], '?');

$live=time()>=$stream['starts_at'] && time()<=$stream['ends_at'];

$status=$live
?"<span class='live'>LIVE</span>"
:"<span class='upcoming'>UPCOMING</span>";

echo "<div class='match'>";

echo "<div class='title'>".$title." ".$status."</div>";

echo "<button onclick='copyLink(\"".htmlspecialchars($link,ENT_QUOTES)."\")'>Copy</button>";

echo "</div>";

}

echo "</div>";

}

?>

</div>

<div
class="toast"
id="toast">

Copied!

</div>

<script>

function copyLink(link){

    // Extra safety
    link = link.split("?")[0];

    navigator.clipboard.writeText(link).then(function(){

        const t = document.getElementById("toast");

        t.innerHTML = "✅ Copied";

        t.style.display = "block";

        setTimeout(function(){

            t.style.display = "none";

        },1500);

    });

}

document
.getElementById("search")
.addEventListener("keyup",function(){

let value=this.value.toLowerCase();

document.querySelectorAll(".match").forEach(function(m){

let txt=m.innerText.toLowerCase();

m.style.display=txt.includes(value)
?"flex"
:"none";

});

});

setTimeout(function(){

location.reload();

},60000);

</script>

</body>

</html>