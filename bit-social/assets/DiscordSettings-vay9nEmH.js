import{x as p,D as h,G as g,z as t,y as o,j as e,T as u,H as f}from"./main-spicy-waves-pick.js";import{$ as y}from"./useSocialTemplates-xIm6yCKT.js";import{M as T}from"./index-YNVrR30_.js";import{T as C,p as l}from"./TemplateDocLink-CY2vTsYz.js";import{P as b}from"./PreviewDummy-BMJegC7P.js";import{C as s}from"./index-BYnHC1Cu.js";import{F as n}from"./index-BR9ObBWj.js";import{S as v}from"./index-Bpfui51h.js";import{S as M}from"./index-CRmb-MJm.js";import{C as c,R as w}from"./row-BTzcosep.js";import"./index-CZOxyE_6.js";import"./EllipsisOutlined-BaGkuCVJ.js";import"./Dropdown-DWF1R22X.js";import"./move-BPAFbN3D.js";import"./EyeOutlined-CISp0hJz.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(a,i){return this.cache.has(a)?this.cache.get(a):(this.cache.set(a,i),i)}};const{Title:P}=u;function _(){const{token:a}=p.useToken(),i=h(y),m=g(f),r=()=>{m(!0)},d=[{label:t("Only custom message"),value:"onlyMessage"},{label:t("Link card"),value:"isLinkCard"},{label:t("Feature image"),value:"isFeaturedImage"},{label:t("Product Image"),value:"isProductImage"},{label:t("All Images"),value:"isAllImages"}];return o(w,{gutter:20,children:[e(c,{span:14,children:o(s,{size:"small",style:{backgroundColor:a.colorFillAlter},children:[o(P,{level:4,children:[" ",t("Discord Template Settings")," "]}),e(C,{platform:"Discord"}),o(s,{children:[e(s.Meta,{description:t("Custom message settings."),title:t("Custom Message")}),e("div",{children:e(T,{onChange:r,rows:5,style:{minWidth:200},value:i.discord.content})})]}),e(s,{style:{marginTop:10},children:o(n,{gap:20,justify:"space-between",children:[e(s.Meta,{description:t("Post styling and type setup."),title:t("Posting type")}),e("div",{children:e(v,{onChange:r,options:d,style:{minWidth:200},value:i.discord.postingType})})]})}),e(s,{style:{marginTop:10},children:o(n,{gap:20,justify:"space-between",children:[e(s.Meta,{description:`Discord restricts the length of a tweet to ${l.discord.description.length} characters. If you enable this option, the first ${l.discord.description.length} characters of your personalized message will be shared; if not, the limit prevents the tweet from being shared.`,title:t("Trim Message")}),e("div",{children:e(M,{checked:i.discord.trimMessage,onChange:r})})]})})]})}),e(c,{span:10,children:e(b,{})})]})}export{_ as default};
