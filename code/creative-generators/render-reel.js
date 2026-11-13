const {chromium}=require('playwright');const {spawn}=require('child_process');const fs=require('fs');
const [a,bb,name]=[+process.argv[2],+process.argv[3],process.argv[4]];
(async()=>{const b=await chromium.launch();const p=await b.newPage({viewport:{width:1080,height:1920}});
await p.goto('file://'+__dirname+'/ad.html');await p.evaluate(()=>window.ready);await p.waitForTimeout(300);
const tmp=`${__dirname}/${name}.part.mp4`;
const ff=spawn('ffmpeg',['-y','-loglevel','error','-f','image2pipe','-framerate','30','-c:v','mjpeg','-i','-','-c:v','libx264','-crf','18','-preset','veryfast','-pix_fmt','yuv420p','-r','30',tmp]);
const n=Math.round((bb-a)*30);
for(let i=0;i<n;i++){await p.evaluate(t=>render(t),a+i/30);const buf=await p.screenshot({type:'jpeg',quality:92});if(!ff.stdin.write(buf))await new Promise(r=>ff.stdin.once('drain',r));}
ff.stdin.end();ff.on('close',c=>{if(c===0){fs.renameSync(tmp,`${__dirname}/${name}.mp4`);console.log(name,'ok',n)}else console.log(name,'fail',c);b.close();});})();
