import{x as h,D as g,G as d,z as t,y as a,j as e,T as u,H as f}from"./main-grumpy-snakes-judge.js";import{$ as y}from"./useSocialTemplates-BXlo0UEi.js";import{M as w}from"./index-Cpljo4TA.js";import{T,p as l}from"./TemplateDocLink-LxZeZr8j.js";import{P as C}from"./PreviewDummy-sIpj3AiZ.js";import{C as s}from"./index-CfejJzE9.js";import{F as n}from"./index-w28iyX2z.js";import{S as b}from"./index-D1eUlGgi.js";import{S as v}from"./index-CmA5oyMA.js";import{C as m,R as M}from"./row-vV5a2ekN.js";import"./index-bPEAZM1C.js";import"./EllipsisOutlined-DOoXFrJg.js";import"./Dropdown-DqQ6naC_.js";import"./move-DbCdynCK.js";import"./EyeOutlined-FfiNH5OE.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(o,i){return this.cache.has(o)?this.cache.get(o):(this.cache.set(o,i),i)}};const{Title:P}=u;function X(){const{token:o}=h.useToken(),i=g(y),c=d(f),r=()=>{c(!0)},p=[{label:t("Only custom message"),value:"onlyMessage"},{label:t("Link card"),value:"isLinkCard"},{label:t("Feature image"),value:"isFeaturedImage"},{label:t("Product Image"),value:"isProductImage"},{label:t("All Images"),value:"isAllImages"}];return a(M,{gutter:20,children:[e(m,{span:14,children:a(s,{size:"small",style:{backgroundColor:o.colorFillAlter},children:[a(P,{level:4,children:[" ",t("X (Twitter) Template Settings")," "]}),e(T,{platform:"X (Twitter)"}),a(s,{children:[e(s.Meta,{description:t("Custom message settings."),title:t("Custom Message")}),e("div",{children:e(w,{onChange:r,rows:5,style:{minWidth:200},value:i.twitter.content})})]}),e(s,{style:{marginTop:10},children:a(n,{gap:20,justify:"space-between",children:[e(s.Meta,{description:t("Post styling and type setup."),title:t("Posting type")}),e("div",{children:e(b,{onChange:r,options:p,style:{minWidth:200},value:i.twitter.postingType})})]})}),e(s,{style:{marginTop:10},children:a(n,{gap:20,justify:"space-between",children:[e(s.Meta,{description:`Twitter restricts the length of a tweet to ${l.twitter.description.length} characters. If you enable this option, the first ${l.twitter.description.length} characters of your personalized message will be shared; if not, the limit prevents the tweet from being shared.`,title:t("Trim Message")}),e("div",{children:e(v,{checked:i.twitter.trimMessage,onChange:r})})]})})]})}),e(m,{span:10,children:e(C,{})})]})}export{X as default};
