import{u as m,c as d,a as i,j as e,_ as g,d as u}from"./main-some-women-speak.js";import{$ as f}from"./_socialTemplates-Dn3A61Ix.js";import{M as y}from"./index-CAwduSRX.js";import{T,p as o}from"./TemplateDocLink-BhGFtmRP.js";import{P}from"./PreviewDummy-BHV2gQSH.js";import{t as v,i as t,F as r,v as C,h as l,j as c,k as b,T as M}from"./antd-Dkhz2BjJ.js";import"./react-query-B4O-PG_a.js";import"./css-in-js-D9KZUreV.js";import"./router-BDBqwsZt.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(a,s){return this.cache.has(a)?this.cache.get(a):(this.cache.set(a,s),s)}};const{Title:k}=M;function O(){const{token:a}=v.useToken(),s=m(f),p=d(u),n=()=>{p(!0)},h=[{label:"Feature image",value:"isFeaturedImage"},{label:"Product Image",value:"isProductImage"},{label:"All images",value:"isAllImages"}];return i(b,{gutter:20,children:[e(c,{span:14,children:i(t,{size:"small",style:{backgroundColor:a.colorFillAlter},children:[i(k,{level:4,children:[" ",g("Pinterest Template Settings")," "]}),e(T,{platform:"Pinterest"}),i(t,{children:[e(t.Meta,{description:"Custom message settings.",title:"Custom Message"}),e("div",{children:e(y,{onChange:n,rows:5,style:{minWidth:200},value:s.pinterest.content})})]}),e(t,{style:{marginTop:10},children:i(r,{gap:20,justify:"space-between",children:[e(t.Meta,{description:"Post styling and type setup.",title:"Posting type"}),e("div",{children:e(C,{onChange:n,options:h,style:{minWidth:200},value:s.pinterest.postingType})})]})}),e(t,{style:{marginTop:10},children:i(r,{gap:20,justify:"space-between",children:[e(t.Meta,{description:`Pinterest restricts the length of a post to ${o.pinterest.description.length} characters. If you enable this option, the first ${o.pinterest.description.length} characters of your personalized message will be shared; if not, the limit prevents the post from being shared.`,title:"Trim Message"}),e("div",{children:e(l,{checked:s.pinterest.trimMessage,onChange:n})})]})}),e(t,{style:{marginTop:10},children:i(r,{gap:20,justify:"space-between",children:[e(t.Meta,{description:"Share the post link on Pinterest.",title:"Post link"}),e("div",{children:e(l,{checked:s.pinterest.isLinkCard,onChange:n})})]})})]})}),e(c,{span:10,children:e(P,{})})]})}export{O as default};
