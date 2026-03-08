const fs=require('fs'); 
const p=process.argv[2]; 
const s=+process.argv[3]; 
const e=+process.argv[4]; 
const a=fs.readFileSync(p,'utf8').split(/\r?\n/); 
