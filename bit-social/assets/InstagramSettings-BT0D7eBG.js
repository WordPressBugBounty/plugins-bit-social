import{u as d,c as u,_ as t,a as s,j as e,d as y}from"./main-plenty-doors-read.js";import{$ as f}from"./useSocialTemplates-DyNqVUWx.js";import{T,p as r}from"./TemplateDocLink-DJQgQTBc.js";import{M as l}from"./index-0DjDwEVr.js";import{P as v}from"./PreviewDummy-B_FhnD8B.js";import{t as C,i as a,F as m,v as M,T as h,h as b,j as c,k as w}from"./antd-Dkhz2BjJ.js";import"./react-query-B4O-PG_a.js";import"./css-in-js-D9KZUreV.js";import"./router-BDBqwsZt.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(n,i){return this.cache.has(n)?this.cache.get(n):(this.cache.set(n,i),i)}};const{Title:I}=h;function O(){const{token:n}=C.useToken(),i=d(f),p=u(y),o=()=>{p(!0)},g=[{label:t("Feature image"),value:"isFeaturedImage"},{label:t("Product Image"),value:"isProductImage"},{label:t("All images"),value:"isAllImages"}];return s(w,{gutter:20,children:[e(c,{span:14,children:s(a,{size:"small",style:{backgroundColor:n.colorFillAlter},children:[s(I,{level:4,children:[" ",t("Instagram Template Settings")," "]}),e(T,{platform:"Instagram"}),s(a,{children:[e(a.Meta,{description:t("Custom message settings."),title:t("Custom Message")}),e("div",{children:e(l,{onChange:o,rows:5,style:{minWidth:200},value:i.instagram.content,wordCount:r.instagram.description.length})})]}),s(a,{style:{marginTop:10},children:[s(m,{gap:20,justify:"space-between",children:[e(a.Meta,{description:t("Post styling and type setup."),title:t("Posting type")}),e("div",{children:e(M,{onChange:o,options:g,style:{minWidth:200},value:i.instagram.postingType})})]}),e("div",{className:"pt-2",children:e(h.Text,{type:"secondary",children:t("Instagram media images must be 1.91:1 to 4:5 aspect ratio, 320-1440px resolution (recommended 1080px), and under 8MB.")})})]}),s(a,{style:{marginTop:10},children:[e(a.Meta,{description:t("Your comment"),title:t("First comment")}),e("div",{children:e(l,{onChange:o,rows:2,style:{minWidth:200},value:i.instagram.comment,wordCount:r.instagram.comment.length})})]}),e(a,{style:{marginTop:10},children:s(m,{gap:20,justify:"space-between",children:[e(a.Meta,{description:`Instagram restricts the length of a post to ${r.instagram.description.length} characters. If you enable this option, the first ${r.instagram.description.length} characters of your personalized message will be shared; if not, the limit prevents the post from being shared.`,title:t("Trim Message")}),e("div",{children:e(b,{checked:i.instagram.trimMessage,onChange:o})})]})})]})}),e(c,{span:10,children:e(v,{})})]})}export{O as default};
