<?php include 'baglantim.php'; ?>
<?php
if(!isset($_SESSION["puan"]))
{
$_SESSION["puan"]=0;
}



 function kelimeSec($value=4,&$sorular,&$kelimeler)
 {
 
	//4lü*2, 5li*2, 6lı*2, 7li*2, 8li*2, 9lu*2, 10lu*2
	// parametreye göre seçilecek (varsayılan 4)
	global $db;
	$kelime_row=$db->query("SELECT * FROM `tbl_oyun` WHERE char_length(kelime)=$value ORDER BY rand() LIMIT 2",PDO::FETCH_ASSOC);
	if ( $kelime_row->rowCount() ){
		foreach( $kelime_row as $row ){
			$sorular[] = $row['soru'];
			$kelimeler[] = $row['kelime'];

		}
	}
}
$soruSayisi=-1;
if(isset($_GET['soru']))
{
	$soruSayisi=$_GET['soru'];
	kelimeSec($soruSayisi+3,$soruDizisi,$kelimeDizisi);
}
else{
	$soruSayisi=1;
	kelimeSec($soruSayisi+3,$soruDizisi,$kelimeDizisi);
}
/*var_dump($a);
var_dump($b);
echo "<hr>";
echo $a[0];
echo "<br>";
echo $b[0];
*/
   ?>
<!DOCTYPE html>
<html>
<head>
	<title>kelime oyunu v1</title>
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript">
	var skor;

if( ! sessionStorage.getItem("puan")){ 
			skor=0; //alert("skor:"+skor)
			sessionStorage.setItem("puan",'0');
		}
	//alert("ilk deger: "+parseInt(sessionStorage.getItem("puan")));

</script>
</head>

<body>
<div class="container">
	<div class="lbl oyun">Kelime Oyunu</div>
	<fieldset class="btn harfler ">
  	<legend>Süre</legend>
  	<div class="kelimePuani" id="sayac">4:00</div>
	</fieldset>
  	<fieldset class="btn harfler ">
  	<legend>Puan</legend>
	<div class="kelimePuani" id="kelimePuani"></div>
	</fieldset>
	  <fieldset class="btn harfler ">
  	<legend>Skor</legend>
	<div class="kelimePuani" id="yarismaPuani">0</div>
	</fieldset>
		  <fieldset class="btn harfler ">
  	<legend>Düşün</legend>
		<div class="kelimePuani gizle" id="dusunmeSure"></div>
</fieldset>
<div id="kelime"></div>
<button onclick="oyun('baslat')" class="btn btn-basla" id="baslat">BAŞLA</button>
<button onclick="harfver();" class="btn btn-harfver gizle" id="harfVer">Harf Alayım</button>
<button onclick="bildiniz(); puanHesapla();" class="btn btn-bildiniz gizle" id="bildiniz">Bildiniz</button>
<button onclick="oyun('durdur');" class="btn btn-durdur gizle" id="durdur">Durdur</button>
<button onclick="yeniSoru(<?php echo $soruSayisi; ?>);" class="btn btn-yeniSoru gizle" id="yeniSoru">Yeni Soru</button>
</div>
</body>
</html>
	<script type="text/javascript">
//----------gosterler-------------
	var sayac= document.getElementById("sayac"); //genel süre sayıcı divi
	var dsure= document.getElementById("dusunmeSure"); //düşünme süresi divi
//----------butonlar-------------
	var btnHarfver=document.getElementById("harfVer");
	var soru,harfKenarlik, harfTutucu;
//----------değişkenler-------------

	var dk=3; var sn=59; //genel süre(4dk) parametreleri	
	var dsn=30; //30sn düşünme süresi
	var genelSure; //genelsüre fonk. değişkeni
	var kelimeText= []; //kelime dizisi(vt)
	var soruText=[];	//soru dizisi(vt)
	var _dusunmeSuresi;
 function dusunmeSuresi() {
 	dsure.classList.remove("gizle");
 	dsure.classList.add("goster");
	btnHarfver.setAttribute("disabled","disabled");
	dsure.innerHTML=((dsn<10)? "0"+dsn: dsn);
	if(dsn>0)	{
 		dsn--;
 		if(dsn==0)
 		{
 			dsn=0;
 			bilemediniz();			
 		}
 		
	}
}

function oyun(secim)
{	
	if(secim=='baslat')
	{		
		btnGoster();
		yukle();//oyun kur
 		sureSayac();
 	}
 	else if(secim=='durdur')
 	{
 		clearInterval(genelSure); //genel süreyi durdur
 		 _dusunmeSuresi=setInterval(dusunmeSuresi,100); //düşünme süresini başlat 30sn todu:(interval 1000 olacak!)
 	}
 	 else if(secim=='duraklat')
 	{
 		clearInterval(genelSure); //genel süreyi durdur 		
 	}
 	else if("devam")
 	{
 		sureSayac();
 	}

}

function sureSayac()
{
	genelSure=setInterval(function() {
		sayac.innerHTML=""+((dk<10)? "0"+dk: dk)+":"+((sn<10)? "0"+sn: sn);
 	if(sn>0)
 	{
 		sn--;
 		if(sn==0)
 		{
 			sn=59;
 			
 			if(dk>0)
 			{
 				dk--;
 				if(dk==0)
 				{
 					dk=0;
 					sn=0;
 				}
 			}
 		}
 	}
 	
 	 },1000);
}
function btnGoster()
{

	document.getElementById("harfVer").classList.remove("gizle");
	document.getElementById("bildiniz").classList.remove("gizle");
	document.getElementById("durdur").classList.remove("gizle");	
	
	document.getElementById("harfVer").classList.add("goster");
	document.getElementById("bildiniz").classList.add("goster");
	document.getElementById("durdur").classList.add("goster");	
	document.getElementById("baslat").classList.add("gizle");
}

var x=0; //soru gurubu 0 veya 1 (örn: 4 harfli ilk soru 0, ikinci 1)
var sayi=[];//rastgele harf indisi oluşturmak için kullanılan dizi

function yukle(){
soruText.push('<?php echo $soruDizisi[0]; ?>');
kelimeText.push('<?php echo $kelimeDizisi[0]; ?>');
soruText.push('<?php echo $soruDizisi[1]; ?>');
kelimeText.push('<?php echo $kelimeDizisi[1]; ?>');

var soruSayisi="<?php echo $soruSayisi; ?>";//kaçıncı soru
			soru=document.createElement("div");
			soru.setAttribute("class","soru");
			soru.setAttribute("id","soru");
			var ast=document.getElementById("kelime").appendChild(soru);
			ast.innerHTML=(soruSayisi)+".soru: "+soruText[x];


	for(var i in kelimeText[x])
	{
		//harf stili oluşturma
		harfKenarlik=document.createElement("div");
		harfKenarlik.setAttribute("class","harfler");
		var harfler_id="harfler"+i;
		harfKenarlik.setAttribute("id",harfler_id);
		document.getElementById("kelime").appendChild(harfKenarlik);
		
		//harfin kendisini ekleme
		harfTutucu=document.createElement("div");
		var harf_id="harf"+i;
		harfTutucu.setAttribute("class","harf");
		harfTutucu.setAttribute("id",harf_id);
		document.getElementById(harfler_id).appendChild(harfTutucu);

		var textNode=document.createTextNode(kelimeText[x][i].toUpperCase());
		harfTutucu.appendChild(textNode);

		
	}	


		while(sayi.length<kelimeText[x].length) 
		{			
			//random dizi oluştur(kelimeyi harfver ile açmak için)
			var r=Math.floor(Math.random()*kelimeText[x].length);				
			if(sayi.indexOf(r)==-1)
			sayi.push(r);		
		}	
	}
	function harfver() 
	{		//harfleri diziden çıkar(pop) ve gizle
		document.getElementById("harf"+sayi.pop()).setAttribute("class","goster");
	}
	function bildiniz() 
	{		
		oyun("duraklat");//yeni soru için 
		clearInterval(_dusunmeSuresi);
		var i=0;
		while(i<kelimeText[x].length) 
		{
			//tüm harfleri göster
			document.getElementById("harf"+(i++)).setAttribute("class","goster");
		}
		document.getElementById("yeniSoru").classList.remove("gizle");
	}

	function puanHesapla()
	{ 
		
			if(!sessionStorage.getItem("puan")){ 
			skor=0; //alert("skor:"+skor)
			sessionStorage.setItem("puan",0);
		}else{ 
			var s=parseInt(sessionStorage.getItem("puan"));
			//alert("s"+s);
		 skor= s+(sayi.length*100);
		//alert("puan:"+skor);
		//alert("sayi:"+sayi.length);
		sessionStorage.setItem("puan",skor);
		//alert("skor"+skor);
		document.getElementById("yarismaPuani").innerHTML=parseInt(sessionStorage.getItem("puan"));
		}
		//sessionStorage.clear(); todo: en son yeni oyunda temizlenecek!
	}

	function bilemediniz() 
	{	
		//bilinmeyen puanı sil
		skor= parseInt(sessionStorage.getItem("puan"))-parseInt(document.getElementById("kelimePuani").innerText);
		sessionStorage.setItem("puan",skor);

		document.getElementById("yeniSoru").classList.remove("gizle");//yeni soru sor
		var i=0;
		while(i<kelimeText[x].length) 
		{			
			document.getElementById("harf"+sayi.pop()).setAttribute("class","goster");
		}

		
	}

	//kelime puantajı(her soru için harf sayısı kadar puan: her harf 100p)
var m= setInterval(function() {
	document.getElementById("kelimePuani").innerHTML=(sayi.length*100);
	document.getElementById("yarismaPuani").innerHTML=sessionStorage.getItem("puan");
		}, 50);

var durum=0;
	function yeniSoru(mevcutSoruNo)
	{
		clearInterval(genelSure);
		oyun("devam");

		if(durum==0)
		{
		
				soru.parentNode.removeChild(soru);
			for(var i in kelimeText[x])
			{
				//yeni soru gelebilmesi için eski karfleri sil
				var e=document.getElementById("harfler"+i);
				e.parentNode.removeChild(e);			
			}
			x=1;
			yukle();
			durum=1;
		}
		else
		{
			var url="?soru="+parseInt(mevcutSoruNo+1)+"";
			window.location=url;
		}
	
	}	

	</script>