import{y as i,ab as n,z as a,j as s,T as r,L as o}from"./main-public-sites-jump.js";const g={description:{length:63206},image:{height:120,width:1500,dimension:235234234,size:{value:10,unit:"MB"},ext:["jpg","jpeg","png"]},gif:{frame:250,size:{value:500,unit:"KB"}},video:{size:{value:100,unit:"MB"},length:{value:30,unit:"minute"},ext:["MP4","MKV"]},errorMessage:{desc:{text:"Text not large than 63206 characters"},media:{text:"Invalid image"}},requirement:{type:"content",message:"Facebook must have media or post content"}},u={description:{length:800},image:{height:200,width:200,dimension:4e4,size:{value:20,unit:"MB"},ext:["jpg","jpeg","png","webp"]},gif:{frame:350,size:{value:15,unit:"MB"}},errorMessage:{desc:{text:"Pinterest post should not larger than 800 characters"},media:{text:"Invalid image"}},requirement:{type:"media",message:"Pinterest must have media"}},m={description:{length:2200},comment:{length:2200},image:{height:120,width:150,dimension:235234234,size:{value:10,unit:"MB"},ext:["jpg","jpeg","png"]},gif:{frame:250,size:{value:500,unit:"KB"}},video:{size:{value:100,unit:"MB"},length:{value:30,unit:"minute"},ext:["MP4","MKV"]},errorMessage:{desc:{text:"Text not large than 2200 characters"},media:{text:"Invalid image"}},requirement:{type:"media",message:"Instagram must have media"}},l={description:{length:3e3},image:{height:0,width:0,dimension:36152320,size:{value:10,unit:"MB"},ext:["jpg","jpeg","png","gif"]},gif:{frame:250,size:{value:0,unit:"MB"}},video:{size:{"min-value":.073,"max-value":200,unit:"MB"},length:{"min-value":.05,"max-value":30,unit:"minute"},ext:["MP4"]},errorMessage:{desc:{text:"LinkedIn post should not larger than 3000 characters"},media:{text:"Invalid image"}},requirement:{type:"content",message:"LinkedIn must have media or post content"}},h={description:{length:280},image:{height:4096,width:4096,dimension:16777216,size:{value:5,unit:"MB"},ext:["jpg","jpeg","png","webp"]},gif:{frame:350,size:{value:15,unit:"MB"}},video:{size:{value:512,unit:"MB"},length:{value:2.33,unit:"minute"},ext:["MP4"]},errorMessage:{desc:{text:"tweet text should not larger than 280 characters"},media:{text:"Invalid image"}},requirement:{type:"content",message:"Twitter must have media or tweet message"}},d={description:{length:4096},image:{height:810,width:540,dimension:16777216,size:{value:20,unit:"MB"},ext:["jpg","jpeg","png","webp"]},gif:{frame:350,size:{value:15,unit:"MB"}},video:{size:{value:512,unit:"MB"},length:{value:2.33,unit:"minute"},ext:["MP4"]},errorMessage:{desc:{text:"tumblr post should not larger than 4096 characters"},media:{text:"Invalid image"}},requirement:{type:"content",message:"Tumblr must have media or post message"}},c={description:{length:750},image:{height:400,width:300,dimension:12e3,size:{value:5,unit:"MB"},ext:["jpg","jpeg","png","webp"]},gif:{frame:350,size:{value:15,unit:"MB"}},video:{size:{value:512,unit:"MB"},length:{value:2.33,unit:"minute"},ext:["MP4"]},errorMessage:{desc:{text:"Google Business Profile text should not larger than 280 characters"},media:{text:"Invalid image"}},requirement:{type:"content",message:"Google Business Profile must have media or update message"}},p={description:{length:2e3},image:{height:5e3,width:5e3,dimension:25e3,size:{value:25,unit:"MB"},ext:["jpg","jpeg","png","webp","gif"]},gif:{frame:350,size:{value:25,unit:"MB"}},video:{size:{value:0,unit:"MB"},length:{value:0,unit:"minute"},ext:["MP4"]},errorMessage:{desc:{text:"Discord text should not larger than 2000 characters"},media:{text:"Invalid image"}},requirement:{type:"content",message:"Discord must have media or update message"}},f={facebook:g,pinterest:u,instagram:m,linkedin:l,twitter:h,tumblr:d,googleBusinessProfile:c,discord:p};globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(e,t){return this.cache.has(e)?this.cache.get(e):(this.cache.set(e,t),t)}};const{Link:v,Paragraph:x}=r;function B({platform:e}){return i(x,{children:[n(a("You can setup %s post settings below."),e),i(v,{className:"pl-2",href:"https://bit-social.com/docs/templates/",rel:"noopener noreferrer nofollow",target:"_blank",underline:!0,children:[a("Doc here."),s(o,{name:"move-up-right",size:12,style:{transform:"translateY(-2px)"}})]})]})}export{B as T,f as p};
