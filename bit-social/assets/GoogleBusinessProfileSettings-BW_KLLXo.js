import{x as h,D as d,G as f,z as t,y as i,j as e,T as y,H as b}from"./main-honest-apples-shout.js";import{$ as P}from"./useSocialTemplates-Yu1e830u.js";import{M as v}from"./index-Dk53L3Lo.js";import{T,p as r}from"./TemplateDocLink-Ch64O6lB.js";import{P as B}from"./PreviewDummy-LpZiuwuP.js";import{C as s}from"./index-WRew3k0q.js";import{F as n}from"./index-Dp2UqEVH.js";import{S as p}from"./index-BbGC-JhL.js";import{S as M}from"./index-BSq1Y2AW.js";import{C as c,R as O}from"./row-C2vQMRT_.js";import"./index-9lCBX2l5.js";import"./EllipsisOutlined-WRnUDywn.js";import"./Dropdown-D4nmV-2U.js";import"./move-BurAjF49.js";import"./EyeOutlined-CLK5SpBj.js";globalThis.jotaiAtomCache=globalThis.jotaiAtomCache||{cache:new Map,get(l,o){return this.cache.has(l)?this.cache.get(l):(this.cache.set(l,o),o)}};const{Title:C}=y;function _(){const{token:l}=h.useToken(),o=d(P),m=f(b),a=()=>{m(!0)},g=[{label:t("Only custom message"),value:"onlyMessage"},{label:t("Feature image"),value:"isFeaturedImage"},{label:t("Product Image"),value:"isProductImage"}],u=[{label:"No Button",value:"none"},{label:"SIGN UP",value:"SIGN_UP"},{label:"LEARN MORE",value:"LEARN_MORE"},{label:"SHOP",value:"SHOP"},{label:"ORDER",value:"ORDER"},{label:"BOOK",value:"BOOK"}];return i(O,{gutter:20,children:[e(c,{span:14,children:i(s,{size:"small",style:{backgroundColor:l.colorFillAlter},children:[i(C,{level:4,children:[" ",t("Google Business Profile Template Settings")," "]}),e(T,{platform:"Google Business Profile"}),i(s,{children:[e(s.Meta,{description:t("Custom message settings."),title:t("Custom Message")}),e("div",{children:e(v,{onChange:a,rows:5,style:{minWidth:200},value:o.googleBusinessProfile.content})})]}),e(s,{style:{marginTop:10},children:i(n,{gap:20,justify:"space-between",children:[e(s.Meta,{description:t("Post styling and type setup."),title:t("Posting type")}),e("div",{children:e(p,{onChange:a,options:g,style:{minWidth:200},value:o.googleBusinessProfile.postingType})})]})}),e(s,{style:{marginTop:10},children:i(n,{gap:20,justify:"space-between",children:[e(s.Meta,{description:t("Select a post link button."),title:t("Add a button")}),e("div",{children:e(p,{onChange:a,options:u,style:{minWidth:200},value:o.googleBusinessProfile.button})})]})}),e(s,{style:{marginTop:10},children:i(n,{gap:20,justify:"space-between",children:[e(s.Meta,{description:`Google Business Profile restricts the length of a post to ${r.googleBusinessProfile.description.length} characters. If you enable this option, the first ${r.googleBusinessProfile.description.length} characters of your personalized message will be shared; if not, the limit prevents the post from being shared.`,title:t("Trim Message")}),e("div",{children:e(M,{checked:o.googleBusinessProfile.trimMessage,onChange:a})})]})})]})}),e(c,{span:10,children:e(B,{})})]})}export{_ as default};
