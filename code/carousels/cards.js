const {chromium}=require('playwright');const path=require('path');const fs=require('fs');
(async()=>{const b=await chromium.launch();const p=await b.newPage({viewport:{width:1080,height:1080}});
fs.mkdirSync('car',{recursive:true});
const sets={C07:['C07_White_Gallery',6],C08:['C08_Hero_vs_Villain',6],C09:['C09_Why_Metal',5]};
for(const [c,[nm,n]] of Object.entries(sets))for(let i=1;i<=n;i++){
 await p.goto('file://'+path.resolve('cards.html')+`?c=${c}&i=${i}`);await p.evaluate(async()=>{await document.fonts.ready;await Promise.all([...document.images].map(i=>i.complete?0:new Promise(r=>{i.onload=i.onerror=r})))});
 await p.screenshot({path:`car/${nm}_0${i}.jpg`,type:'jpeg',quality:93,clip:{x:0,y:0,width:1080,height:1080}});}
await b.close();})();
