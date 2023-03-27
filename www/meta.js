const formatDate = (date = new Date()) => {
  return `${date.getDate().toString().padStart(2, 0)}-${(date.getMonth() + 1).toString().padStart(2, 0)}-${date.getFullYear()}`;
}
const formatPercent = (v) => {
 return v!==undefined?(''+v+(v!='-'?'%':'')):'-';
}
const orgsEx = (v) => {
 return v?`<a target="_blank" href="${baseurl}orgsex.php?inn=${v}"> ${v}</a>`:'-';
}

var baseurl='';
var nsort=1;
var isnum=false;

function metas(v,p)
{
 var mets='';
 v.forEach(x=>{
mets+=`<tr><td style="width:300px;"><a href="https://zakupki.gov.ru/44fz/filestore/public/1.0/download/priz/file.html?uid=${x[0]}">${x[1]}</a></td><td>${x[2]}</td><td>${x[3]}</td></tr>`;
});
 return mets?`<a href="${baseurl}metaall.php?purnumber=${p}"> Все метаданные по закупке</a><table border=1 style="width:600px"><tr>${mets}</tr></table>`:'-';
}

function rinsert(t,e)
{
 var row = t.insertRow(t.rows.length);
 if(e[0]) row.outerHTML=e[0];
}

function buildcells()
{
 var t=document.getElementById('ress');
 var tt=document.getElementById('bress');
 var numcells=t.rows[0].cells.length;
 var temps=[];
 var nums=[];
 temps.fill(numcells,undefined);
 nums.fill(numcells,false);
 for(var i=0;i<numcells;++i)
 {
  var c=t.rows[0].cells[i];
  var tm=c.getAttribute('template');
  if(tm){
   if(!tm.includes('return'))tm='return '+tm;
   temps[i]=new Function('v,e',tm);
  }
  if(c.classList.contains('num')) nums[i]=true;
 }
 arr.forEach(e => {
 var r = tt.insertRow(tt.rows.length-1);
 for(var i=0;i<numcells;++i)
 {
  var c=t.rows[0].cells[i];
  var cl=r.insertCell(i);
  var a=c.getAttribute('fid');
  var tmpstr = '';
  if(temps[i])
  {
    tmpstr = temps[i].call(null,a?e[a]:undefined,e);
  }else{
   if(a)
   {
    tmpstr = nums[i]?(e[a]?e[a]:'-'):(e[a]===undefined?'':e[a]);
   }
  }
  cl.innerHTML = tmpstr;
  cl.classList.add('mstext');
 }
 e[0]=r.outerHTML;
 });
}

function fsort(a,b,r)
{
 if(a>b) return r?-1:1;
 if(a<b) return r?1:-1;
 return 0;
}

function fnsort(a,b,r)
{
 var anan=typeof(a)!="number";
 var bnan=typeof(b)!="number";
 if(bnan&&anan) return 0;
 if(anan) return 1;
 if(bnan) return -1;
 if(a>b) return r?-1:1;
 if(a<b) return r?1:-1;
 return 0;
}

function showsort()
{
 var els = document.getElementsByClassName('sort');
 var xsort=Math.abs(nsort);
 Array.prototype.forEach.call(els,x=>{
var tmp=x.getAttribute('fid');
if(xsort==tmp){
if(x.classList.contains('sortasc'))x.classList.remove('sortasc');
if(x.classList.contains('sortdesc'))x.classList.remove('sortdesc');
if(x.classList.contains('sortable'))x.classList.remove('sortable');
x.classList.add(nsort>0?'sortasc':'sortdesc');
}else if(!x.classList.contains('sortable'))
{
if(x.classList.contains('sortasc'))x.classList.remove('sortasc');
if(x.classList.contains('sortdesc'))x.classList.remove('sortdesc');
x.classList.add('sortable');
}
});
}

function ffill()
{
 var nnsort=Math.abs(nsort);
 if(isnum)
 {arr.sort((a,b)=>fnsort(a[nnsort],b[nnsort],nsort<0));}
 else
 {arr.sort((a,b)=>fsort(a[nnsort],b[nnsort],nsort<0));}
// if(nsort<0) arr.reverse();
 var t = document.getElementById('bress');
 while(t.rows.length>0){t.deleteRow(t.rows.length-1);}
 arr.forEach(e => rinsert(t,e));
 showsort();
}

function createTable()
{
 var t = document.getElementById("ress");
 var c = t.cloneNode(true);
// arr.forEach(e => rinsert(c,e));
 return xlshdr+c.outerHTML+"</body></html>";
}

function downloadxl() {
  const b = new Blob([createTable()], {type: 'application/octet-stream'});
  var e = document.createElement('a');
  e.href = window.URL.createObjectURL(b);
  e.setAttribute('download', "purchases.xls");

  e.style.display = 'none';
  document.body.appendChild(e);

  e.click();

  document.body.removeChild(e);
}

function sortBy(e)
{
var tmp=e.target.getAttribute('fid');
isnum=e.target.classList.contains("num");
if(tmp)
{
 if(Math.abs(nsort)==tmp)nsort=-nsort;else nsort=tmp;
 ffill();
}
}

function floaded()
{
 baseurl=window.location.origin+window.location.pathname.substring(0,location.pathname.lastIndexOf("/")+1);
 var els = document.getElementsByClassName('sort');
 Array.prototype.forEach.call(els,x=>x.addEventListener("click", function(event){sortBy(event)}));
 buildcells();
 ffill();     
}