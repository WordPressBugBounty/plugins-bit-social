import{x as d,D as u,G as f,z as t,y as a,j as e,T as p,H as y}from"./main-spicy-news-hang.js";import{$ as T}from"./useSocialTemplates-C91stQIp.js";import{T as C,p as n}from"./TemplateDocLink-DQylpyvs.js";import{M as l}from"./index-CEj32310.js";import{P as v}from"./PreviewDummy-BfuVsPkt.js";import{C as s}from"./index-CM_XzolY.js";import{F as m}from"./index-BANaAk1n.js";import{S as M}from"./index-WU5SHOC8.js";import{S as b}from"./index-BzpnhhDw.js";import{C as c,R as w}from"./row-DPIcWscG.js";import"./index-D9i9nAtp.js";import"./EllipsisOutlined-Cte7H3t5.js";import"./Dropdown-Bgk_N0Mz.js";import"./move-wK8wZ7aw.js";import"./EyeOutlined-B_5AVqR3.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(o,i){return this.cache.has(o)?this.cache.get(o):(this.cache.set(o,i),i)}};const{Title:I}=p;function _(){const{token:o}=d.useToken(),i=u(T),g=f(y),r=()=>{g(!0)},h=[{label:t("Feature image"),value:"isFeaturedImage"},{label:t("Product Image"),value:"isProductImage"},{label:t("All images"),value:"isAllImages"}];return a(w,{gutter:20,children:[e(c,{span:14,children:a(s,{size:"small",style:{backgroundColor:o.colorFillAlter},children:[a(I,{level:4,children:[" ",t("Instagram Template Settings")," "]}),e(C,{platform:"Instagram"}),a(s,{children:[e(s.Meta,{description:t("Custom message settings."),title:t("Custom Message")}),e("div",{children:e(l,{onChange:r,rows:5,style:{minWidth:200},value:i.instagram.content,wordCount:n.instagram.description.length})})]}),a(s,{style:{marginTop:10},children:[a(m,{gap:20,justify:"space-between",children:[e(s.Meta,{description:t("Post styling and type setup."),title:t("Posting type")}),e("div",{children:e(M,{onChange:r,options:h,style:{minWidth:200},value:i.instagram.postingType})})]}),e("div",{className:"pt-2",children:e(p.Text,{type:"secondary",children:t("Instagram media images must be 1.91:1 to 4:5 aspect ratio, 320-1440px resolution (recommended 1080px), and under 8MB.")})})]}),a(s,{style:{marginTop:10},children:[e(s.Meta,{description:t("Your comment"),title:t("First comment")}),e("div",{children:e(l,{onChange:r,rows:2,style:{minWidth:200},value:i.instagram.comment,wordCount:n.instagram.comment.length})})]}),e(s,{style:{marginTop:10},children:a(m,{gap:20,justify:"space-between",children:[e(s.Meta,{description:`Instagram restricts the length of a post to ${n.instagram.description.length} characters. If you enable this option, the first ${n.instagram.description.length} characters of your personalized message will be shared; if not, the limit prevents the post from being shared.`,title:t("Trim Message")}),e("div",{children:e(b,{checked:i.instagram.trimMessage,onChange:r})})]})})]})}),e(c,{span:10,children:e(v,{})})]})}export{_ as default};
