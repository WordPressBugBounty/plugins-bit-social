import{x as b,W as w,z as s,y as i,j as e,T as g,X as x}from"./main-spicy-news-hang.js";import{$ as I}from"./useSocialTemplates-C91stQIp.js";import{u as S,a as j}from"./useMemoDebounce-C2a2gl5b.js";import{u as A}from"./useUpdateSocialTemplates-D1bX9dQK.js";import{P}from"./PreviewDummy-BfuVsPkt.js";import{p as m,T as F}from"./TemplateDocLink-DQylpyvs.js";import{M as l}from"./index-CEj32310.js";import{s as k}from"./index-zlExXddV.js";import{R as W,C as c}from"./row-DPIcWscG.js";import{C as o}from"./index-CM_XzolY.js";import{F as p}from"./index-BANaAk1n.js";import{S as $}from"./index-WU5SHOC8.js";import{S as D}from"./index-BzpnhhDw.js";import"./index-D9i9nAtp.js";import"./context-BKqqcez3.js";import"./EllipsisOutlined-Cte7H3t5.js";import"./Dropdown-Bgk_N0Mz.js";import"./move-wK8wZ7aw.js";import"./EyeOutlined-B_5AVqR3.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(r,a){return this.cache.has(r)?this.cache.get(r):(this.cache.set(r,a),a)}};const{Title:z}=g;function te(){const{token:r}=b.useToken(),[a,d]=w(I),{isTemplatesUpdateSuccessfully:h,updateSocialTemplates:u}=A(),[f,y]=k.useMessage();S(async()=>{const{status:t}=await u(a);h&&t&&j(f,t)},400,[a]);const n=(t,C)=>{d(v=>x(v,M=>{M.instagram[t]=C}))},T=[{label:s("Feature image"),value:"isFeaturedImage"},{label:s("Product Image"),value:"isProductImage"},{label:s("All images"),value:"isAllImages"}];return i(o,{style:{backgroundColor:r.colorFillAlter},children:[y,i(W,{gutter:20,children:[i(c,{span:14,children:[i(z,{level:4,children:[s("Instagram Template Settings")," "]}),e(F,{platform:"Instagram"}),i(o,{children:[e(o.Meta,{description:s("Custom message settings."),title:s("Custom Message")}),e("div",{children:e(l,{onChange:t=>n("content",t),rows:5,style:{minWidth:200},value:a.instagram.content,wordCount:m.instagram.description.length})})]}),i(o,{style:{marginTop:10},children:[i(p,{gap:20,justify:"space-between",children:[e(o.Meta,{description:s("Post styling and type setup."),title:s("Posting type")}),e("div",{children:e($,{onChange:t=>n("postingType",t),options:T,style:{minWidth:200},value:a.instagram.postingType})})]}),e("div",{className:"pt-2",children:e(g.Text,{type:"secondary",children:s("Instagram media images must be 1.91:1 to 4:5 aspect ratio, 320-1440px resolution (recommended 1080px), and under 8MB.")})})]}),i(o,{style:{marginTop:10},children:[e(o.Meta,{description:s("Your comment"),title:s("First comment")}),e("div",{children:e(l,{onChange:t=>n("comment",t),rows:1,style:{minWidth:200},value:a.instagram.comment,wordCount:m.instagram.comment.length})})]}),e(o,{style:{marginTop:10},children:i(p,{gap:20,justify:"space-between",children:[e(o.Meta,{description:`Instagram restricts the length of a post to ${m.instagram.description.length} characters. If you enable this option, the first ${m.instagram.description.length} characters of your personalized message will be shared; if not, the limit prevents the post from being shared.`,title:s("Trim Message")}),e("div",{children:e(D,{checked:a.instagram.trimMessage,onChange:t=>n("trimMessage",t)})})]})})]}),e(c,{span:10,children:e(P,{})})]})]})}export{te as default};
