import{u as m,c as p,a,j as e,_ as d,d as g}from"./main-some-women-speak.js";import{$ as u}from"./_socialTemplates-Dn3A61Ix.js";import{M as y}from"./index-CAwduSRX.js";import{T as f,p as r}from"./TemplateDocLink-BhGFtmRP.js";import{P as w}from"./PreviewDummy-BHV2gQSH.js";import{t as T,i as t,F as l,v,h as b,j as n,k as C,T as M}from"./antd-Dkhz2BjJ.js";import"./react-query-B4O-PG_a.js";import"./css-in-js-D9KZUreV.js";import"./router-BDBqwsZt.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(i,s){return this.cache.has(i)?this.cache.get(i):(this.cache.set(i,s),s)}};const{Title:j}=M;function O(){const{token:i}=T.useToken(),s=m(u),c=p(g),o=()=>{c(!0)},h=[{label:"Only custom message",value:"onlyMessage"},{label:"Link card",value:"isLinkCard"},{label:"Feature image",value:"isFeaturedImage"},{label:"Product Image",value:"isProductImage"},{label:"All Images",value:"isAllImages"}];return a(C,{gutter:20,children:[e(n,{span:14,children:a(t,{size:"small",style:{backgroundColor:i.colorFillAlter},children:[a(j,{level:4,children:[" ",d("X (Twitter) Template Settings")," "]}),e(f,{platform:"X (Twitter)"}),a(t,{children:[e(t.Meta,{description:"Custom message settings.",title:"Custom Message"}),e("div",{children:e(y,{onChange:o,rows:5,style:{minWidth:200},value:s.twitter.content})})]}),e(t,{style:{marginTop:10},children:a(l,{gap:20,justify:"space-between",children:[e(t.Meta,{description:"Post styling and type setup.",title:"Posting type"}),e("div",{children:e(v,{onChange:o,options:h,style:{minWidth:200},value:s.twitter.postingType})})]})}),e(t,{style:{marginTop:10},children:a(l,{gap:20,justify:"space-between",children:[e(t.Meta,{description:`Twitter restricts the length of a tweet to ${r.twitter.description.length} characters. If you enable this option, the first ${r.twitter.description.length} characters of your personalized message will be shared; if not, the limit prevents the tweet from being shared.`,title:"Trim Message"}),e("div",{children:e(b,{checked:s.twitter.trimMessage,onChange:o})})]})})]})}),e(n,{span:10,children:e(w,{})})]})}export{O as default};
