import{h as b,a,_ as l,j as e,p as w}from"./main-some-women-speak.js";import{$ as I}from"./_socialTemplates-Dn3A61Ix.js";import{u as x,a as j}from"./useMemoDebounce-C2yfnHyS.js";import{u as S}from"./useUpdateSocialTemplates-CWZQ699N.js";import{P as A}from"./PreviewDummy-BHV2gQSH.js";import{p as r,T as P}from"./TemplateDocLink-BhGFtmRP.js";import{M as m}from"./index-CAwduSRX.js";import{t as k,w as F,k as $,j as c,i,F as p,v as D,T as g,h as W}from"./antd-Dkhz2BjJ.js";import"./react-query-B4O-PG_a.js";import"./css-in-js-D9KZUreV.js";import"./router-BDBqwsZt.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(o,s){return this.cache.has(o)?this.cache.get(o):(this.cache.set(o,s),s)}};const{Title:_}=g;function G(){const{token:o}=k.useToken(),[s,h]=b(I),{isTemplatesUpdateSuccessfully:d,updateSocialTemplates:u}=S(),[y,f]=F.useMessage();x(async()=>{const{status:t}=await u(s);d&&t&&j(y,t)},400,[s]);const n=(t,v)=>{h(M=>w(M,C=>{C.instagram[t]=v}))},T=[{label:"Feature image",value:"isFeaturedImage"},{label:"Product Image",value:"isProductImage"},{label:"All images",value:"isAllImages"}];return a(i,{style:{backgroundColor:o.colorFillAlter},children:[f,a($,{gutter:20,children:[a(c,{span:14,children:[a(_,{level:4,children:[l("Instagram Template Settings")," "]}),e(P,{platform:"Instagram"}),a(i,{children:[e(i.Meta,{description:"Custom message settings.",title:"Custom Message"}),e("div",{children:e(m,{onChange:t=>n("content",t),rows:5,style:{minWidth:200},value:s.instagram.content,wordCount:r.instagram.description.length})})]}),a(i,{style:{marginTop:10},children:[a(p,{gap:20,justify:"space-between",children:[e(i.Meta,{description:"Post styling and type setup.",title:"Posting type"}),e("div",{children:e(D,{onChange:t=>n("postingType",t),options:T,style:{minWidth:200},value:s.instagram.postingType})})]}),e("div",{className:"pt-2",children:e(g.Text,{type:"secondary",children:l("Instagram media images must be 1.91:1 to 4:5 aspect ratio, 320-1440px resolution (recommended 1080px), and under 8MB.")})})]}),a(i,{style:{marginTop:10},children:[e(i.Meta,{description:"Your comment",title:"First comment"}),e("div",{children:e(m,{onChange:t=>n("comment",t),rows:1,style:{minWidth:200},value:s.instagram.comment,wordCount:r.instagram.comment.length})})]}),e(i,{style:{marginTop:10},children:a(p,{gap:20,justify:"space-between",children:[e(i.Meta,{description:`Instagram restricts the length of a post to ${r.instagram.description.length} characters. If you enable this option, the first ${r.instagram.description.length} characters of your personalized message will be shared; if not, the limit prevents the post from being shared.`,title:"Trim Message"}),e("div",{children:e(W,{checked:s.instagram.trimMessage,onChange:t=>n("trimMessage",t)})})]})})]}),e(c,{span:10,children:e(A,{})})]})]})}export{G as default};
