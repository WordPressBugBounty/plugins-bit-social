import{a2 as j,w,r as g,D as A,E as S,G as M,H as z,j as a,ag as B,y as i,L as u,B as m,S as L,z as P,T as $}from"./main-spicy-news-hang.js";import{F as C}from"./index-BANaAk1n.js";import{P as I}from"./index-D9i9nAtp.js";import{I as q}from"./index-WU5SHOC8.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};function E(){const{data:t,isLoading:e}=j({queryFn:async()=>w("smart-tags",void 0,void 0,"GET"),queryKey:["smart-tags"]});return{isSmartTagsLoading:e,smartTags:(t==null?void 0:t.data)||{}}}globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};const{Title:F}=$;var O={name:"5bhc30",styles:"margin-bottom:8px"};function _({label:t,onChange:e,value:l,wordCount:p,wrapperClassName:f,...d}){const{smartTags:T}=E(),c=g.useRef(null),{isProClient:n}=A(S),b=M(z),v=(s,r)=>{if(r==="pro"&&!n){b(!0);return}const o=`${l} {${s}}`;c.current&&(c.current.value=o),e==null||e(o)},x=s=>{e==null||e(s.target.value)},y=a(L,{align:"start",direction:"vertical",size:0,children:Object.values(T).map(({description:s,key:r,label:o,type:h})=>a(B,{title:s,children:i(m,{block:!0,onClick:()=>v(r,h),size:"small",type:"text",children:[o," ",!n&&h==="pro"&&a(u,{color:"#ff8609",name:"crown",size:18})]},r)},r))});return i("div",{className:f,children:[i(C,{align:"center",css:O,justify:"space-between",children:[a(F,{level:5,children:t}),a(I,{content:y,overlayInnerStyle:{maxHeight:"60vh",overflow:"auto"},placement:"bottom",trigger:"click",children:a(m,{icon:a(u,{name:"tags"}),size:"small",type:"text",children:P("Show Smart Tags")})})]}),a(q.TextArea,{maxLength:p,onChange:x,ref:c,showCount:!0,value:l,...d})]})}const k=g.memo(_);globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(t,e){return this.cache.has(t)?this.cache.get(t):(this.cache.set(t,e),e)}};export{k as M};
