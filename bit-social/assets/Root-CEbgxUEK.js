import{_ as c,r as h,p as b,j as a,a as o,L as u}from"./main-plenty-doors-read.js";import{u as g}from"./react-query-B4O-PG_a.js";import{t as A,k as T,j as v,F as d,A as j,i as x,T as m,n as p,y as C,B as L}from"./antd-Dkhz2BjJ.js";import{L as w}from"./router-BDBqwsZt.js";import"./css-in-js-D9KZUreV.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};const k=[{count:0,icon:"mouse-pointer-click",slug:"active_account_count",title:c("Active Account")},{count:0,icon:"text",slug:"published_post_count",title:c("Published Post")},{count:0,icon:"clock",slug:"active_schedule_count",title:c("Active Schedule")}];function y(){const{data:t,isLoading:e}=g({queryFn:async()=>{const{data:s}=await h("analytics",void 0,void 0,"GET");return k.map(i=>b(i,f=>{f.count=(s==null?void 0:s[i.slug])||0}))},queryKey:["analytics"]});return{analytics:t||[],isAnalyticsLoading:e}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};const{Title:r}=m;function _(){const{token:t}=A.useToken(),{analytics:e}=y();return a("div",{className:"mt-5",children:a(T,{gutter:20,children:e.map(s=>a(v,{span:6,children:o(x,{children:[o(d,{className:"mb-2",justify:"space-between",children:[a(r,{level:2,style:{color:t.colorPrimary,marginBottom:0},children:s.count}),a(j,{icon:a(u,{name:s.icon}),size:60,style:{background:t.colorPrimaryBg,color:t.colorText,transform:"translate(4px, -10px)"}})]}),a(r,{level:5,style:{marginBottom:0},children:s.title})]})},s.slug))})})}const B=""+new URL("bit-social-ba-assets-983.svg",import.meta.url).href;globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};function N(){const{data:t,isLoading:e}=g({queryFn:async()=>h("user-info",void 0,void 0,"GET"),queryKey:["user-info"]});return{isUserInfoLoading:e,userInfo:t==null?void 0:t.data}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};const{Text:n,Title:l}=m;function S(){const{isUserInfoLoading:t,userInfo:e}=N();return t?a(p,{}):o(d,{align:"center",justify:"center",style:{paddingBottom:40,position:"relative"},vertical:!0,children:[o(l,{style:{left:"30px",position:"absolute",top:"20px"},children:[c("Hi")," ",e==null?void 0:e.username]}),a(C,{className:"mt-2",height:"300px",preview:!1,src:B}),a(l,{className:"mt-3",level:3,children:c("Welcome to Bit Social")}),a(n,{style:{color:"blue"},children:c("Thanks for using Bit Social")}),a(n,{children:c("Smart solution for WordPress post Schedule")}),a(w,{className:"mt-2",to:"/accounts",children:o(L,{size:"large",type:"primary",children:[c("Accounts"),a(u,{name:"move-up-right",style:{transform:"translateY(-2px)"}})]})})," "]})}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};function F(){const{analytics:t,isAnalyticsLoading:e}=y();return e?a(p,{}):t.some(i=>Number(i.count)>0)?a(_,{}):a(S,{})}export{F as default};
