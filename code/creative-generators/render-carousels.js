const {chromium}=require('playwright');
(async()=>{const b=await chromium.launch();const p=await b.newPage({viewport:{width:1080,height:1080}});
await p.goto('file://'+__dirname+'/car.html');await p.evaluate(()=>window.ready);
for(const k of ['A0','A1','A2','A3','A4','A5','A6','B0','B1','B2','B3','B4','B5','B6']){await p.evaluate(k=>show(k),k);await p.waitForTimeout(250);await p.screenshot({path:`${__dirname}/car/${k}.png`});}
await b.close();})();
