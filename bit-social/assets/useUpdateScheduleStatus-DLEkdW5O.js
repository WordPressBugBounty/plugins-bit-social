import{j as e,F as a,a as n,L as s,r as i}from"./main-afraid-ants-sort.js";import{T as c,l}from"./antd-CMhliQbc.js";import{b as h}from"./react-query-BAnB2mAm.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,o){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,o),o)}};const u=t=>{switch(t){case"accounts":return n(a,{children:[e("b",{children:"Accounts:"})," To add your desired social platform, click on the"," ",e("b",{children:'"Connect Account"'})," button. A modal will appear displaying various social platforms where you will find two options. First one is ",e("b",{children:'"Connect"'})," button to connect your platform directly and another one is ",e("b",{children:'"custom app"'})," option which will work by entering your personal app credentials.",e("br",{}),e("br",{}),"To set it up, click on the ",e("b",{children:'"Connect Account"'})," button and follow the next step!"," ",n("a",{href:"https://bit-social.com/docs/accounts/",rel:"noopener noreferrer",target:"_blank",children:["Learn more ",e(s,{name:"move-up-right",style:{transform:"translateY(-2px)"}})]})]});case"schedule":return n(a,{children:[e("b",{children:"Schedules:"})," You can automatically schedule your Publish WordPress posts to be shared on your social media platforms. This feature allows you to filter your posts, ensuring that only the content you want to share at your chosen times.",e("br",{}),e("br",{}),"To set it up, click on the ",e("b",{children:'"Create Schedule"'})," button and follow the next step!"," ",n("a",{href:"https://bit-social.com/docs/schedule-wordpress-posts-on-social-media/",rel:"noopener noreferrer",target:"_blank",children:["Learn more ",e(s,{name:"move-up-right",style:{transform:"translateY(-2px)"}})]})]});case"shareNow":return n(a,{children:[e("b",{children:"Share Now:"})," You can instantly share and automatically schedule your written posts to be shared on your social media platforms. There you will find a save draft (to save your content for future), publish (to share instantly), and setup schedule option to ensure that the content you have written will be shared at your chosen times.",e("br",{}),e("br",{}),"To set it up, click on the ",e("b",{children:'"Share Post"'})," button and follow the next step!"," ",n("a",{href:"https://bit-social.com/docs/instantly-share-posts-on-social-media-platforms/",rel:"noopener noreferrer",target:"_blank",children:["Learn more ",e(s,{name:"move-up-right",style:{transform:"translateY(-2px)"}})]})]});default:return e(a,{children:"test"})}};function b({fontSize:t="1rem",section:o,style:r}){return e(l,{description:e(c.Text,{style:{fontSize:t},children:u(o)}),style:r,type:"info"})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,o){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,o),o)}};function f(){const{isPending:t,mutateAsync:o}=h({mutationFn:async r=>{if(!r.id)throw new Error("Schedule ID is undefined");return i(`schedule/${r.id}/update-status`,{...r},void 0,"POST")}});return{isUpdateScheduleLoading:t,updateScheduleStatus:r=>o(r)}}export{b as N,f as u};
