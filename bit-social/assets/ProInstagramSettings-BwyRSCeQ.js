import{h as C,a as i,j as e,p as b}from"./main-afraid-ants-sort.js";import{$ as w}from"./_socialTemplates-CRuryL5C.js";import{u as I,a as x}from"./useMemoDebounce-DLzpEz7x.js";import{u as j}from"./useUpdateSocialTemplates-BduuTD5H.js";import{P as S}from"./PreviewDummy-CZePD3yE.js";import{p as r,T as A}from"./TemplateDocLink-DOw8aIDd.js";import{M as l}from"./index-Bxjp1ha4.js";import{t as P,v as F,j as k,i as m,h as a,F as c,u as $,T as p,g as D}from"./antd-CMhliQbc.js";import"./react-query-BAnB2mAm.js";import"./css-in-js-5B1lZlTz.js";import"./router-yX2GsLRQ.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(o,s){return this.cache.has(o)?this.cache.get(o):(this.cache.set(o,s),s)}};const{Title:W}=p;function G(){const{token:o}=P.useToken(),[s,g]=C(w),{isTemplatesUpdateSuccessfully:h,updateSocialTemplates:d}=j(),[u,y]=F.useMessage();I(async()=>{const{status:t}=await d(s);h&&t&&x(u,t)},400,[s]);const n=(t,T)=>{g(v=>b(v,M=>{M.instagram[t]=T}))},f=[{label:"Feature image",value:"isFeaturedImage"},{label:"Product Image",value:"isProductImage"},{label:"All images",value:"isAllImages"}];return i(a,{style:{backgroundColor:o.colorFillAlter},children:[y,i(k,{gutter:20,children:[i(m,{span:14,children:[e(W,{level:4,children:"Instagram Template Settings"}),e(A,{platform:"Instagram"}),i(a,{children:[e(a.Meta,{description:"Custom message settings.",title:"Custom Message"}),e("div",{children:e(l,{onChange:t=>n("content",t),rows:5,style:{minWidth:200},value:s.instagram.content,wordCount:r.instagram.description.length})})]}),i(a,{style:{marginTop:10},children:[i(c,{gap:20,justify:"space-between",children:[e(a.Meta,{description:"Post styling and type setup.",title:"Posting type"}),e("div",{children:e($,{onChange:t=>n("postingType",t),options:f,style:{minWidth:200},value:s.instagram.postingType})})]}),e("div",{className:"pt-2",children:e(p.Text,{type:"secondary",children:"Instagram media images must be 1.91:1 to 4:5 aspect ratio, 320-1440px resolution (recommended 1080px), and under 8MB."})})]}),i(a,{style:{marginTop:10},children:[e(a.Meta,{description:"Your comment",title:"First comment"}),e("div",{children:e(l,{onChange:t=>n("comment",t),rows:1,style:{minWidth:200},value:s.instagram.comment,wordCount:r.instagram.comment.length})})]}),e(a,{style:{marginTop:10},children:i(c,{gap:20,justify:"space-between",children:[e(a.Meta,{description:`Instagram restricts the length of a post to ${r.instagram.description.length} characters. If you enable this option, the first ${r.instagram.description.length} characters of your personalized message will be shared; if not, the limit prevents the post from being shared.`,title:"Trim Message"}),e("div",{children:e(D,{checked:s.instagram.trimMessage,onChange:t=>n("trimMessage",t)})})]})})]}),e(m,{span:10,children:e(S,{})})]})]})}export{G as default};
