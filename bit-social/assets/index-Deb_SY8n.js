import{r as l,i as We,bS as He,k as Be,l as ee,m as it,bT as ye,aN as Ce,aB as Ie,O as G,C as st,b as Y,aC as ct,bU as ut,ba as mt,aF as dt,n as ie,p as ft,bV as pt,bW as gt,bX as ht,bY as bt,bZ as De,b_ as re,b$ as yt,N as Ct,aM as me,aK as Ee,bJ as vt,c as xt,aY as $t,b3 as Fe,c0 as St,u as qe,an as wt,f as Ot,aG as Ae,ag as It,aH as ke,al as Et,bO as ve,aj as Ft,aE as Pt,b7 as Nt,o as Xe,c1 as Ge,c2 as jt,c3 as Mt,c4 as Rt,c5 as _t,b1 as Vt,aD as Tt,bb as Lt,c6 as zt,c7 as Wt,B as Ht,c8 as Bt,c9 as Dt,e as qt}from"./main-grumpy-snakes-judge.js";import{g as At}from"./collapse-BbEVqHco.js";import{C as Ke,R as kt}from"./row-vV5a2ekN.js";import{b as Xt,g as Pe,P as Gt}from"./index-bPEAZM1C.js";const Ne=e=>typeof e=="object"&&e!=null&&e.nodeType===1,je=(e,t)=>(!t||e!=="hidden")&&e!=="visible"&&e!=="clip",he=(e,t)=>{if(e.clientHeight<e.scrollHeight||e.clientWidth<e.scrollWidth){const r=getComputedStyle(e,null);return je(r.overflowY,t)||je(r.overflowX,t)||(n=>{const o=(a=>{if(!a.ownerDocument||!a.ownerDocument.defaultView)return null;try{return a.ownerDocument.defaultView.frameElement}catch{return null}})(n);return!!o&&(o.clientHeight<n.scrollHeight||o.clientWidth<n.scrollWidth)})(e)}return!1},ue=(e,t,r,n,o,a,i,c)=>a<e&&i>t||a>e&&i<t?0:a<=e&&c<=r||i>=t&&c>=r?a-e-n:i>t&&c<r||a<e&&c>r?i-t+o:0,Kt=e=>{const t=e.parentElement;return t??(e.getRootNode().host||null)},Me=(e,t)=>{var r,n,o,a;if(typeof document>"u")return[];const{scrollMode:i,block:c,inline:s,boundary:m,skipOverflowHiddenElements:x}=t,f=typeof m=="function"?m:W=>W!==m;if(!Ne(e))throw new TypeError("Invalid target");const $=document.scrollingElement||document.documentElement,N=[];let S=e;for(;Ne(S)&&f(S);){if(S=Kt(S),S===$){N.push(S);break}S!=null&&S===document.body&&he(S)&&!he(document.documentElement)||S!=null&&he(S,x)&&N.push(S)}const w=(n=(r=window.visualViewport)==null?void 0:r.width)!=null?n:innerWidth,b=(a=(o=window.visualViewport)==null?void 0:o.height)!=null?a:innerHeight,{scrollX:y,scrollY:F}=window,{height:u,width:g,top:d,right:v,bottom:R,left:P}=e.getBoundingClientRect(),{top:C,right:O,bottom:_,left:D}=(W=>{const p=window.getComputedStyle(W);return{top:parseFloat(p.scrollMarginTop)||0,right:parseFloat(p.scrollMarginRight)||0,bottom:parseFloat(p.scrollMarginBottom)||0,left:parseFloat(p.scrollMarginLeft)||0}})(e);let j=c==="start"||c==="nearest"?d-C:c==="end"?R+_:d+u/2-C+_,I=s==="center"?P+g/2-D+O:s==="end"?v+O:P-D;const T=[];for(let W=0;W<N.length;W++){const p=N[W],{height:z,width:L,top:q,right:H,bottom:A,left:U}=p.getBoundingClientRect();if(i==="if-needed"&&d>=0&&P>=0&&R<=b&&v<=w&&d>=q&&R<=A&&P>=U&&v<=H)return T;const te=getComputedStyle(p),Q=parseInt(te.borderLeftWidth,10),k=parseInt(te.borderTopWidth,10),h=parseInt(te.borderRightWidth,10),M=parseInt(te.borderBottomWidth,10);let E=0,B=0;const X="offsetWidth"in p?p.offsetWidth-p.clientWidth-Q-h:0,J="offsetHeight"in p?p.offsetHeight-p.clientHeight-k-M:0,oe="offsetWidth"in p?p.offsetWidth===0?0:L/p.offsetWidth:0,ne="offsetHeight"in p?p.offsetHeight===0?0:z/p.offsetHeight:0;if($===p)E=c==="start"?j:c==="end"?j-b:c==="nearest"?ue(F,F+b,b,k,M,F+j,F+j+u,u):j-b/2,B=s==="start"?I:s==="center"?I-w/2:s==="end"?I-w:ue(y,y+w,w,Q,h,y+I,y+I+g,g),E=Math.max(0,E+F),B=Math.max(0,B+y);else{E=c==="start"?j-q-k:c==="end"?j-A+M+J:c==="nearest"?ue(q,A,z,k,M+J,j,j+u,u):j-(q+z/2)+J/2,B=s==="start"?I-U-Q:s==="center"?I-(U+L/2)+X/2:s==="end"?I-H+h+X:ue(U,H,L,Q,h+X,I,I+g,g);const{scrollLeft:V,scrollTop:Z}=p;E=ne===0?0:Math.max(0,Math.min(Z+E/ne,p.scrollHeight-z/ne+J)),B=oe===0?0:Math.max(0,Math.min(V+B/oe,p.scrollWidth-L/oe+X)),j+=Z-E,I+=V-B}T.push({el:p,top:E,left:B})}return T},Yt=e=>e===!1?{block:"end",inline:"nearest"}:(t=>t===Object(t)&&Object.keys(t).length!==0)(e)?e:{block:"start",inline:"nearest"};function Ut(e,t){if(!e.isConnected||!(o=>{let a=o;for(;a&&a.parentNode;){if(a.parentNode===document)return!0;a=a.parentNode instanceof ShadowRoot?a.parentNode.host:a.parentNode}return!1})(e))return;const r=(o=>{const a=window.getComputedStyle(o);return{top:parseFloat(a.scrollMarginTop)||0,right:parseFloat(a.scrollMarginRight)||0,bottom:parseFloat(a.scrollMarginBottom)||0,left:parseFloat(a.scrollMarginLeft)||0}})(e);if((o=>typeof o=="object"&&typeof o.behavior=="function")(t))return t.behavior(Me(e,t));const n=typeof t=="boolean"||t==null?void 0:t.behavior;for(const{el:o,top:a,left:i}of Me(e,Yt(t))){const c=a-r.top+r.bottom,s=i-r.left+r.right;o.scroll({top:c,left:s,behavior:n})}}function de(e){const[t,r]=l.useState(e);return l.useEffect(()=>{const n=setTimeout(()=>{r(e)},e.length?0:10);return()=>{clearTimeout(n)}},[e]),t}const Qt=e=>{const{componentCls:t}=e,r=`${t}-show-help`,n=`${t}-show-help-item`;return{[r]:{transition:`opacity ${e.motionDurationFast} ${e.motionEaseInOut}`,"&-appear, &-enter":{opacity:0,"&-active":{opacity:1}},"&-leave":{opacity:1,"&-active":{opacity:0}},[n]:{overflow:"hidden",transition:`height ${e.motionDurationFast} ${e.motionEaseInOut},
                     opacity ${e.motionDurationFast} ${e.motionEaseInOut},
                     transform ${e.motionDurationFast} ${e.motionEaseInOut} !important`,[`&${n}-appear, &${n}-enter`]:{transform:"translateY(-5px)",opacity:0,"&-active":{transform:"translateY(0)",opacity:1}},[`&${n}-leave-active`]:{transform:"translateY(-5px)"}}}}},Jt=e=>({legend:{display:"block",width:"100%",marginBottom:e.marginLG,padding:0,color:e.colorTextDescription,fontSize:e.fontSizeLG,lineHeight:"inherit",border:0,borderBottom:`${ee(e.lineWidth)} ${e.lineType} ${e.colorBorder}`},'input[type="search"]':{boxSizing:"border-box"},'input[type="radio"], input[type="checkbox"]':{lineHeight:"normal"},'input[type="file"]':{display:"block"},'input[type="range"]':{display:"block",width:"100%"},"select[multiple], select[size]":{height:"auto"},"input[type='file']:focus,\n  input[type='radio']:focus,\n  input[type='checkbox']:focus":{outline:0,boxShadow:`0 0 0 ${ee(e.controlOutlineWidth)} ${e.controlOutline}`},output:{display:"block",paddingTop:15,color:e.colorText,fontSize:e.fontSize,lineHeight:e.lineHeight}}),Re=(e,t)=>{const{formItemCls:r}=e;return{[r]:{[`${r}-label > label`]:{height:t},[`${r}-control-input`]:{minHeight:t}}}},Zt=e=>{const{componentCls:t}=e;return{[e.componentCls]:Object.assign(Object.assign(Object.assign({},Be(e)),Jt(e)),{[`${t}-text`]:{display:"inline-block",paddingInlineEnd:e.paddingSM},"&-small":Object.assign({},Re(e,e.controlHeightSM)),"&-large":Object.assign({},Re(e,e.controlHeightLG))})}},en=e=>{const{formItemCls:t,iconCls:r,componentCls:n,rootPrefixCls:o,antCls:a,labelRequiredMarkColor:i,labelColor:c,labelFontSize:s,labelHeight:m,labelColonMarginInlineStart:x,labelColonMarginInlineEnd:f,itemMarginBottom:$}=e;return{[t]:Object.assign(Object.assign({},Be(e)),{marginBottom:$,verticalAlign:"top","&-with-help":{transition:"none"},[`&-hidden,
        &-hidden${a}-row`]:{display:"none"},"&-has-warning":{[`${t}-split`]:{color:e.colorError}},"&-has-error":{[`${t}-split`]:{color:e.colorWarning}},[`${t}-label`]:{flexGrow:0,overflow:"hidden",whiteSpace:"nowrap",textAlign:"end",verticalAlign:"middle","&-left":{textAlign:"start"},"&-wrap":{overflow:"unset",lineHeight:e.lineHeight,whiteSpace:"unset"},"> label":{position:"relative",display:"inline-flex",alignItems:"center",maxWidth:"100%",height:m,color:c,fontSize:s,[`> ${r}`]:{fontSize:e.fontSize,verticalAlign:"top"},[`&${t}-required:not(${t}-required-mark-optional)::before`]:{display:"inline-block",marginInlineEnd:e.marginXXS,color:i,fontSize:e.fontSize,fontFamily:"SimSun, sans-serif",lineHeight:1,content:'"*"',[`${n}-hide-required-mark &`]:{display:"none"}},[`${t}-optional`]:{display:"inline-block",marginInlineStart:e.marginXXS,color:e.colorTextDescription,[`${n}-hide-required-mark &`]:{display:"none"}},[`${t}-tooltip`]:{color:e.colorTextDescription,cursor:"help",writingMode:"horizontal-tb",marginInlineStart:e.marginXXS},"&::after":{content:'":"',position:"relative",marginBlock:0,marginInlineStart:x,marginInlineEnd:f},[`&${t}-no-colon::after`]:{content:'"\\a0"'}}},[`${t}-control`]:{"--ant-display":"flex",flexDirection:"column",flexGrow:1,[`&:first-child:not([class^="'${o}-col-'"]):not([class*="' ${o}-col-'"])`]:{width:"100%"},"&-input":{position:"relative",display:"flex",alignItems:"center",minHeight:e.controlHeight,"&-content":{flex:"auto",maxWidth:"100%"}}},[t]:{"&-additional":{display:"flex",flexDirection:"column"},"&-explain, &-extra":{clear:"both",color:e.colorTextDescription,fontSize:e.fontSize,lineHeight:e.lineHeight},"&-explain-connected":{width:"100%"},"&-extra":{minHeight:e.controlHeightSM,transition:`color ${e.motionDurationMid} ${e.motionEaseOut}`},"&-explain":{"&-error":{color:e.colorError},"&-warning":{color:e.colorWarning}}},[`&-with-help ${t}-explain`]:{height:"auto",opacity:1},[`${t}-feedback-icon`]:{fontSize:e.fontSize,textAlign:"center",visibility:"visible",animationName:He,animationDuration:e.motionDurationMid,animationTimingFunction:e.motionEaseOutBack,pointerEvents:"none","&-success":{color:e.colorSuccess},"&-error":{color:e.colorError},"&-warning":{color:e.colorWarning},"&-validating":{color:e.colorPrimary}}})}},_e=(e,t)=>{const{formItemCls:r}=e;return{[`${t}-horizontal`]:{[`${r}-label`]:{flexGrow:0},[`${r}-control`]:{flex:"1 1 0",minWidth:0},[`${r}-label[class$='-24'], ${r}-label[class*='-24 ']`]:{[`& + ${r}-control`]:{minWidth:"unset"}}}}},tn=e=>{const{componentCls:t,formItemCls:r,inlineItemMarginBottom:n}=e;return{[`${t}-inline`]:{display:"flex",flexWrap:"wrap",[r]:{flex:"none",marginInlineEnd:e.margin,marginBottom:n,"&-row":{flexWrap:"nowrap"},[`> ${r}-label,
        > ${r}-control`]:{display:"inline-block",verticalAlign:"top"},[`> ${r}-label`]:{flex:"none"},[`${t}-text`]:{display:"inline-block"},[`${r}-has-feedback`]:{display:"inline-block"}}}}},K=e=>({padding:e.verticalLabelPadding,margin:e.verticalLabelMargin,whiteSpace:"initial",textAlign:"start","> label":{margin:0,"&::after":{visibility:"hidden"}}}),Ye=e=>{const{componentCls:t,formItemCls:r,rootPrefixCls:n}=e;return{[`${r} ${r}-label`]:K(e),[`${t}:not(${t}-inline)`]:{[r]:{flexWrap:"wrap",[`${r}-label, ${r}-control`]:{[`&:not([class*=" ${n}-col-xs"])`]:{flex:"0 0 100%",maxWidth:"100%"}}}}}},nn=e=>{const{componentCls:t,formItemCls:r,antCls:n}=e;return{[`${t}-vertical`]:{[`${r}:not(${r}-horizontal)`]:{[`${r}-row`]:{flexDirection:"column"},[`${r}-label > label`]:{height:"auto"},[`${r}-control`]:{width:"100%"},[`${r}-label,
        ${n}-col-24${r}-label,
        ${n}-col-xl-24${r}-label`]:K(e)}},[`@media (max-width: ${ee(e.screenXSMax)})`]:[Ye(e),{[t]:{[`${r}:not(${r}-horizontal)`]:{[`${n}-col-xs-24${r}-label`]:K(e)}}}],[`@media (max-width: ${ee(e.screenSMMax)})`]:{[t]:{[`${r}:not(${r}-horizontal)`]:{[`${n}-col-sm-24${r}-label`]:K(e)}}},[`@media (max-width: ${ee(e.screenMDMax)})`]:{[t]:{[`${r}:not(${r}-horizontal)`]:{[`${n}-col-md-24${r}-label`]:K(e)}}},[`@media (max-width: ${ee(e.screenLGMax)})`]:{[t]:{[`${r}:not(${r}-horizontal)`]:{[`${n}-col-lg-24${r}-label`]:K(e)}}}}},rn=e=>{const{formItemCls:t,antCls:r}=e;return{[`${t}-vertical`]:{[`${t}-row`]:{flexDirection:"column"},[`${t}-label > label`]:{height:"auto"},[`${t}-control`]:{width:"100%"}},[`${t}-vertical ${t}-label,
      ${r}-col-24${t}-label,
      ${r}-col-xl-24${t}-label`]:K(e),[`@media (max-width: ${ee(e.screenXSMax)})`]:[Ye(e),{[t]:{[`${r}-col-xs-24${t}-label`]:K(e)}}],[`@media (max-width: ${ee(e.screenSMMax)})`]:{[t]:{[`${r}-col-sm-24${t}-label`]:K(e)}},[`@media (max-width: ${ee(e.screenMDMax)})`]:{[t]:{[`${r}-col-md-24${t}-label`]:K(e)}},[`@media (max-width: ${ee(e.screenLGMax)})`]:{[t]:{[`${r}-col-lg-24${t}-label`]:K(e)}}}},on=e=>({labelRequiredMarkColor:e.colorError,labelColor:e.colorTextHeading,labelFontSize:e.fontSize,labelHeight:e.controlHeight,labelColonMarginInlineStart:e.marginXXS/2,labelColonMarginInlineEnd:e.marginXS,itemMarginBottom:e.marginLG,verticalLabelPadding:`0 0 ${e.paddingXS}px`,verticalLabelMargin:0,inlineItemMarginBottom:0}),Ue=(e,t)=>it(e,{formItemCls:`${e.componentCls}-item`,rootPrefixCls:t}),xe=We("Form",(e,t)=>{let{rootPrefixCls:r}=t;const n=Ue(e,r);return[Zt(n),en(n),Qt(n),_e(n,n.componentCls),_e(n,n.formItemCls),tn(n),nn(n),rn(n),At(n),He]},on,{order:-1e3}),Ve=[];function be(e,t,r){let n=arguments.length>3&&arguments[3]!==void 0?arguments[3]:0;return{key:typeof e=="string"?e:`${t}-${n}`,error:e,errorStatus:r}}const Qe=e=>{let{help:t,helpStatus:r,errors:n=Ve,warnings:o=Ve,className:a,fieldId:i,onVisibleChanged:c}=e;const{prefixCls:s}=l.useContext(ye),m=`${s}-item-explain`,x=Ce(s),[f,$,N]=xe(s,x),S=l.useMemo(()=>Ie(s),[s]),w=de(n),b=de(o),y=l.useMemo(()=>t!=null?[be(t,"help",r)]:[].concat(G(w.map((g,d)=>be(g,"error","error",d))),G(b.map((g,d)=>be(g,"warning","warning",d)))),[t,r,w,b]),F=l.useMemo(()=>{const g={};return y.forEach(d=>{let{key:v}=d;g[v]=(g[v]||0)+1}),y.map((d,v)=>Object.assign(Object.assign({},d),{key:g[d.key]>1?`${d.key}-fallback-${v}`:d.key}))},[y]),u={};return i&&(u.id=`${i}_help`),f(l.createElement(st,{motionDeadline:S.motionDeadline,motionName:`${s}-show-help`,visible:!!F.length,onVisibleChanged:c},g=>{const{className:d,style:v}=g;return l.createElement("div",Object.assign({},u,{className:Y(m,d,N,x,a,$),style:v,role:"alert"}),l.createElement(ct,Object.assign({keys:F},Ie(s),{motionName:`${s}-show-help-item`,component:!1}),R=>{const{key:P,error:C,errorStatus:O,className:_,style:D}=R;return l.createElement("div",{key:P,className:Y(_,{[`${m}-${O}`]:O}),style:D},C)}))}))},ln=["parentNode"],an="form_item";function se(e){return e===void 0||e===!1?[]:Array.isArray(e)?e:[e]}function Je(e,t){if(!e.length)return;const r=e.join("_");return t?`${t}_${r}`:ln.includes(r)?`${an}_${r}`:r}function Ze(e,t,r,n,o,a){let i=n;return a!==void 0?i=a:r.validating?i="validating":e.length?i="error":t.length?i="warning":(r.touched||o&&r.validated)&&(i="success"),i}function Te(e){return se(e).join("_")}function Le(e,t){const r=t.getFieldInstance(e),n=mt(r);if(n)return n;const o=Je(se(e),t.__INTERNAL__.name);if(o)return document.getElementById(o)}function et(e){const[t]=ut(),r=l.useRef({}),n=l.useMemo(()=>e??Object.assign(Object.assign({},t),{__INTERNAL__:{itemRef:o=>a=>{const i=Te(o);a?r.current[i]=a:delete r.current[i]}},scrollToField:function(o){let a=arguments.length>1&&arguments[1]!==void 0?arguments[1]:{};const i=Le(o,n);i&&Ut(i,Object.assign({scrollMode:"if-needed",block:"nearest"},a))},focusField:o=>{var a;const i=Le(o,n);i&&((a=i.focus)===null||a===void 0||a.call(i))},getFieldInstance:o=>{const a=Te(o);return r.current[a]}}),[e,t]);return[n]}var sn=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(r[n[o]]=e[n[o]]);return r};const cn=(e,t)=>{const r=l.useContext(dt),{getPrefixCls:n,direction:o,form:a}=l.useContext(ie),{prefixCls:i,className:c,rootClassName:s,size:m,disabled:x=r,form:f,colon:$,labelAlign:N,labelWrap:S,labelCol:w,wrapperCol:b,hideRequiredMark:y,layout:F="horizontal",scrollToFirstError:u,requiredMark:g,onFinishFailed:d,name:v,style:R,feedbackIcons:P,variant:C}=e,O=sn(e,["prefixCls","className","rootClassName","size","disabled","form","colon","labelAlign","labelWrap","labelCol","wrapperCol","hideRequiredMark","layout","scrollToFirstError","requiredMark","onFinishFailed","name","style","feedbackIcons","variant"]),_=ft(m),D=l.useContext(pt),j=l.useMemo(()=>g!==void 0?g:y?!1:a&&a.requiredMark!==void 0?a.requiredMark:!0,[y,g,a]),I=$??(a==null?void 0:a.colon),T=n("form",i),W=Ce(T),[p,z,L]=xe(T,W),q=Y(T,`${T}-${F}`,{[`${T}-hide-required-mark`]:j===!1,[`${T}-rtl`]:o==="rtl",[`${T}-${_}`]:_},L,W,z,a==null?void 0:a.className,c,s),[H]=et(f),{__INTERNAL__:A}=H;A.name=v;const U=l.useMemo(()=>({name:v,labelAlign:N,labelCol:w,labelWrap:S,wrapperCol:b,vertical:F==="vertical",colon:I,requiredMark:j,itemRef:A.itemRef,form:H,feedbackIcons:P}),[v,N,w,b,F,I,j,H,P]),te=l.useRef(null);l.useImperativeHandle(t,()=>{var h;return Object.assign(Object.assign({},H),{nativeElement:(h=te.current)===null||h===void 0?void 0:h.nativeElement})});const Q=(h,M)=>{if(h){let E={block:"nearest"};typeof h=="object"&&(E=Object.assign(Object.assign({},E),h)),H.scrollToField(M,E),E.focus&&H.focusField(M)}},k=h=>{if(d==null||d(h),h.errorFields.length){const M=h.errorFields[0].name;if(u!==void 0){Q(u,M);return}a&&a.scrollToFirstError!==void 0&&Q(a.scrollToFirstError,M)}};return p(l.createElement(gt.Provider,{value:C},l.createElement(ht,{disabled:x},l.createElement(bt.Provider,{value:_},l.createElement(De,{validateMessages:D},l.createElement(re.Provider,{value:U},l.createElement(yt,Object.assign({id:v},O,{name:v,onFinishFailed:k,form:H,ref:te,style:Object.assign(Object.assign({},a==null?void 0:a.style),R),className:q}))))))))},un=l.forwardRef(cn);function mn(e){if(typeof e=="function")return e;const t=Ct(e);return t.length<=1?t[0]:t}const tt=()=>{const{status:e,errors:t=[],warnings:r=[]}=l.useContext(me);return{status:e,errors:t,warnings:r}};tt.Context=me;function dn(e){const[t,r]=l.useState(e),n=l.useRef(null),o=l.useRef([]),a=l.useRef(!1);l.useEffect(()=>(a.current=!1,()=>{a.current=!0,Ee.cancel(n.current),n.current=null}),[]);function i(c){a.current||(n.current===null&&(o.current=[],n.current=Ee(()=>{n.current=null,r(s=>{let m=s;return o.current.forEach(x=>{m=x(m)}),m})})),o.current.push(c))}return[t,i]}function fn(){const{itemRef:e}=l.useContext(re),t=l.useRef({});function r(n,o){const a=o&&typeof o=="object"&&vt(o),i=n.join("_");return(t.current.name!==i||t.current.originRef!==a)&&(t.current.name=i,t.current.originRef=a,t.current.ref=xt(e(n),a)),t.current.ref}return r}const pn=e=>{const{formItemCls:t}=e;return{"@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none)":{[`${t}-control`]:{display:"flex"}}}},gn=$t(["Form","item-item"],(e,t)=>{let{rootPrefixCls:r}=t;const n=Ue(e,r);return[pn(n)]});var hn=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(r[n[o]]=e[n[o]]);return r};const bn=24,yn=e=>{const{prefixCls:t,status:r,labelCol:n,wrapperCol:o,children:a,errors:i,warnings:c,_internalItemRender:s,extra:m,help:x,fieldId:f,marginBottom:$,onErrorVisibleChanged:N,label:S}=e,w=`${t}-item`,b=l.useContext(re),y=l.useMemo(()=>{let I=Object.assign({},o||b.wrapperCol||{});return S===null&&!n&&!o&&b.labelCol&&[void 0,"xs","sm","md","lg","xl","xxl"].forEach(W=>{const p=W?[W]:[],z=Fe(b.labelCol,p),L=typeof z=="object"?z:{},q=Fe(I,p),H=typeof q=="object"?q:{};"span"in L&&!("offset"in H)&&L.span<bn&&(I=St(I,[].concat(p,["offset"]),L.span))}),I},[o,b]),F=Y(`${w}-control`,y.className),u=l.useMemo(()=>hn(b,["labelCol","wrapperCol"]),[b]),g=l.useRef(null),[d,v]=l.useState(0);qe(()=>{m&&g.current?v(g.current.clientHeight):v(0)},[m]);const R=l.createElement("div",{className:`${w}-control-input`},l.createElement("div",{className:`${w}-control-input-content`},a)),P=l.useMemo(()=>({prefixCls:t,status:r}),[t,r]),C=$!==null||i.length||c.length?l.createElement(ye.Provider,{value:P},l.createElement(Qe,{fieldId:f,errors:i,warnings:c,help:x,helpStatus:r,className:`${w}-explain-connected`,onVisibleChanged:N})):null,O={};f&&(O.id=`${f}_extra`);const _=m?l.createElement("div",Object.assign({},O,{className:`${w}-extra`,ref:g}),m):null,D=C||_?l.createElement("div",{className:`${w}-additional`,style:$?{minHeight:$+d}:{}},C,_):null,j=s&&s.mark==="pro_table_render"&&s.render?s.render(e,{input:R,errorList:C,extra:_}):l.createElement(l.Fragment,null,R,D);return l.createElement(re.Provider,{value:u},l.createElement(Ke,Object.assign({},y,{className:F}),j),l.createElement(gn,{prefixCls:t}))};var Cn={icon:{tag:"svg",attrs:{viewBox:"64 64 896 896",focusable:"false"},children:[{tag:"path",attrs:{d:"M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm0 820c-205.4 0-372-166.6-372-372s166.6-372 372-372 372 166.6 372 372-166.6 372-372 372z"}},{tag:"path",attrs:{d:"M623.6 316.7C593.6 290.4 554 276 512 276s-81.6 14.5-111.6 40.7C369.2 344 352 380.7 352 420v7.6c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8V420c0-44.1 43.1-80 96-80s96 35.9 96 80c0 31.1-22 59.6-56.1 72.7-21.2 8.1-39.2 22.3-52.1 40.9-13.1 19-19.9 41.8-19.9 64.9V620c0 4.4 3.6 8 8 8h48c4.4 0 8-3.6 8-8v-22.7a48.3 48.3 0 0130.9-44.8c59-22.7 97.1-74.7 97.1-132.5.1-39.3-17.1-76-48.3-103.3zM472 732a40 40 0 1080 0 40 40 0 10-80 0z"}}]},name:"question-circle",theme:"outlined"},vn=function(t,r){return l.createElement(wt,Ot({},t,{ref:r,icon:Cn}))},xn=l.forwardRef(vn),$n=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(r[n[o]]=e[n[o]]);return r};function Sn(e){return e?typeof e=="object"&&!l.isValidElement(e)?e:{title:e}:null}const wn=e=>{let{prefixCls:t,label:r,htmlFor:n,labelCol:o,labelAlign:a,colon:i,required:c,requiredMark:s,tooltip:m,vertical:x}=e;var f;const[$]=Ae("Form"),{labelAlign:N,labelCol:S,labelWrap:w,colon:b}=l.useContext(re);if(!r)return null;const y=o||S||{},F=a||N,u=`${t}-item-label`,g=Y(u,F==="left"&&`${u}-left`,y.className,{[`${u}-wrap`]:!!w});let d=r;const v=i===!0||b!==!1&&i!==!1;v&&!x&&typeof r=="string"&&r.trim()&&(d=r.replace(/[:|：]\s*$/,""));const P=Sn(m);if(P){const{icon:D=l.createElement(xn,null)}=P,j=$n(P,["icon"]),I=l.createElement(It,Object.assign({},j),l.cloneElement(D,{className:`${t}-item-tooltip`,title:"",onClick:T=>{T.preventDefault()},tabIndex:null}));d=l.createElement(l.Fragment,null,d,I)}const C=s==="optional",O=typeof s=="function";O?d=s(d,{required:!!c}):C&&!c&&(d=l.createElement(l.Fragment,null,d,l.createElement("span",{className:`${t}-item-optional`,title:""},($==null?void 0:$.optional)||((f=ke.Form)===null||f===void 0?void 0:f.optional))));const _=Y({[`${t}-item-required`]:c,[`${t}-item-required-mark-optional`]:C||O,[`${t}-item-no-colon`]:!v});return l.createElement(Ke,Object.assign({},y,{className:g}),l.createElement("label",{htmlFor:n,className:_,title:typeof r=="string"?r:""},d))},On={success:Et,warning:ve,error:Ft,validating:Pt};function nt(e){let{children:t,errors:r,warnings:n,hasFeedback:o,validateStatus:a,prefixCls:i,meta:c,noStyle:s}=e;const m=`${i}-item`,{feedbackIcons:x}=l.useContext(re),f=Ze(r,n,c,null,!!o,a),{isFormItemInput:$,status:N,hasFeedback:S,feedbackIcon:w}=l.useContext(me),b=l.useMemo(()=>{var y;let F;if(o){const g=o!==!0&&o.icons||x,d=f&&((y=g==null?void 0:g({status:f,errors:r,warnings:n}))===null||y===void 0?void 0:y[f]),v=f&&On[f];F=d!==!1&&v?l.createElement("span",{className:Y(`${m}-feedback-icon`,`${m}-feedback-icon-${f}`)},d||l.createElement(v,null)):null}const u={status:f||"",errors:r,warnings:n,hasFeedback:!!o,feedbackIcon:F,isFormItemInput:!0};return s&&(u.status=(f??N)||"",u.isFormItemInput=$,u.hasFeedback=!!(o??S),u.feedbackIcon=o!==void 0?u.feedbackIcon:w),u},[f,o,s,$,N]);return l.createElement(me.Provider,{value:b},t)}var In=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(r[n[o]]=e[n[o]]);return r};function En(e){const{prefixCls:t,className:r,rootClassName:n,style:o,help:a,errors:i,warnings:c,validateStatus:s,meta:m,hasFeedback:x,hidden:f,children:$,fieldId:N,required:S,isRequired:w,onSubItemMetaChange:b,layout:y}=e,F=In(e,["prefixCls","className","rootClassName","style","help","errors","warnings","validateStatus","meta","hasFeedback","hidden","children","fieldId","required","isRequired","onSubItemMetaChange","layout"]),u=`${t}-item`,{requiredMark:g,vertical:d}=l.useContext(re),v=d||y==="vertical",R=l.useRef(null),P=de(i),C=de(c),O=a!=null,_=!!(O||i.length||c.length),D=!!R.current&&Nt(R.current),[j,I]=l.useState(null);qe(()=>{if(_&&R.current){const L=getComputedStyle(R.current);I(parseInt(L.marginBottom,10))}},[_,D]);const T=L=>{L||I(null)},p=function(){let L=arguments.length>0&&arguments[0]!==void 0?arguments[0]:!1;const q=L?P:m.errors,H=L?C:m.warnings;return Ze(q,H,m,"",!!x,s)}(),z=Y(u,r,n,{[`${u}-with-help`]:O||P.length||C.length,[`${u}-has-feedback`]:p&&x,[`${u}-has-success`]:p==="success",[`${u}-has-warning`]:p==="warning",[`${u}-has-error`]:p==="error",[`${u}-is-validating`]:p==="validating",[`${u}-hidden`]:f,[`${u}-${y}`]:y});return l.createElement("div",{className:z,style:o,ref:R},l.createElement(kt,Object.assign({className:`${u}-row`},Xe(F,["_internalItemRender","colon","dependencies","extra","fieldKey","getValueFromEvent","getValueProps","htmlFor","id","initialValue","isListField","label","labelAlign","labelCol","labelWrap","messageVariables","name","normalize","noStyle","preserve","requiredMark","rules","shouldUpdate","trigger","tooltip","validateFirst","validateTrigger","valuePropName","wrapperCol","validateDebounce"])),l.createElement(wn,Object.assign({htmlFor:N},e,{requiredMark:g,required:S??w,prefixCls:t,vertical:v})),l.createElement(yn,Object.assign({},e,m,{errors:P,warnings:C,prefixCls:t,status:p,help:a,marginBottom:j,onErrorVisibleChanged:T}),l.createElement(Ge.Provider,{value:b},l.createElement(nt,{prefixCls:t,meta:m,errors:m.errors,warnings:m.warnings,hasFeedback:x,validateStatus:p},$)))),!!j&&l.createElement("div",{className:`${u}-margin-offset`,style:{marginBottom:-j}}))}const Fn="__SPLIT__";function Pn(e,t){const r=Object.keys(e),n=Object.keys(t);return r.length===n.length&&r.every(o=>{const a=e[o],i=t[o];return a===i||typeof a=="function"||typeof i=="function"})}const Nn=l.memo(e=>{let{children:t}=e;return t},(e,t)=>Pn(e.control,t.control)&&e.update===t.update&&e.childProps.length===t.childProps.length&&e.childProps.every((r,n)=>r===t.childProps[n]));function ze(){return{errors:[],warnings:[],touched:!1,validating:!1,name:[],validated:!1}}function jn(e){const{name:t,noStyle:r,className:n,dependencies:o,prefixCls:a,shouldUpdate:i,rules:c,children:s,required:m,label:x,messageVariables:f,trigger:$="onChange",validateTrigger:N,hidden:S,help:w,layout:b}=e,{getPrefixCls:y}=l.useContext(ie),{name:F}=l.useContext(re),u=mn(s),g=typeof u=="function",d=l.useContext(Ge),{validateTrigger:v}=l.useContext(jt),R=N!==void 0?N:v,P=t!=null,C=y("form",a),O=Ce(C),[_,D,j]=xe(C,O);Lt();const I=l.useContext(Mt),T=l.useRef(null),[W,p]=dn({}),[z,L]=Rt(()=>ze()),q=h=>{const M=I==null?void 0:I.getKey(h.name);if(L(h.destroy?ze():h,!0),r&&w!==!1&&d){let E=h.name;if(h.destroy)E=T.current||E;else if(M!==void 0){const[B,X]=M;E=[B].concat(G(X)),T.current=E}d(h,E)}},H=(h,M)=>{p(E=>{const B=Object.assign({},E),J=[].concat(G(h.name.slice(0,-1)),G(M)).join(Fn);return h.destroy?delete B[J]:B[J]=h,B})},[A,U]=l.useMemo(()=>{const h=G(z.errors),M=G(z.warnings);return Object.values(W).forEach(E=>{h.push.apply(h,G(E.errors||[])),M.push.apply(M,G(E.warnings||[]))}),[h,M]},[W,z.errors,z.warnings]),te=fn();function Q(h,M,E){return r&&!S?l.createElement(nt,{prefixCls:C,hasFeedback:e.hasFeedback,validateStatus:e.validateStatus,meta:z,errors:A,warnings:U,noStyle:!0},h):l.createElement(En,Object.assign({key:"row"},e,{className:Y(n,j,O,D),prefixCls:C,fieldId:M,isRequired:E,errors:A,warnings:U,meta:z,onSubItemMetaChange:H,layout:b}),h)}if(!P&&!g&&!o)return _(Q(u));let k={};return typeof x=="string"?k.label=x:t&&(k.label=String(t)),f&&(k=Object.assign(Object.assign({},k),f)),_(l.createElement(_t,Object.assign({},e,{messageVariables:k,trigger:$,validateTrigger:R,onMetaChange:q}),(h,M,E)=>{const B=se(t).length&&M?M.name:[],X=Je(B,F),J=m!==void 0?m:!!(c!=null&&c.some(V=>{if(V&&typeof V=="object"&&V.required&&!V.warningOnly)return!0;if(typeof V=="function"){const Z=V(E);return(Z==null?void 0:Z.required)&&!(Z!=null&&Z.warningOnly)}return!1})),oe=Object.assign({},h);let ne=null;if(Array.isArray(u)&&P)ne=u;else if(!(g&&(!(i||o)||P))){if(!(o&&!g&&!P))if(l.isValidElement(u)){const V=Object.assign(Object.assign({},u.props),oe);if(V.id||(V.id=X),w||A.length>0||U.length>0||e.extra){const ae=[];(w||A.length>0)&&ae.push(`${X}_help`),e.extra&&ae.push(`${X}_extra`),V["aria-describedby"]=ae.join(" ")}A.length>0&&(V["aria-invalid"]="true"),J&&(V["aria-required"]="true"),Vt(u)&&(V.ref=te(B,u)),new Set([].concat(G(se($)),G(se(R)))).forEach(ae=>{V[ae]=function(){for(var $e,Se,fe,we,pe,Oe=arguments.length,ge=new Array(Oe),ce=0;ce<Oe;ce++)ge[ce]=arguments[ce];(fe=oe[ae])===null||fe===void 0||($e=fe).call.apply($e,[oe].concat(ge)),(pe=(we=u.props)[ae])===null||pe===void 0||(Se=pe).call.apply(Se,[we].concat(ge))}});const at=[V["aria-required"],V["aria-invalid"],V["aria-describedby"]];ne=l.createElement(Nn,{control:oe,update:u,childProps:at},Tt(u,V))}else g&&(i||o)&&!P?ne=u(E):ne=u}return Q(ne,X,J)}))}const rt=jn;rt.useStatus=tt;var Mn=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(r[n[o]]=e[n[o]]);return r};const Rn=e=>{var{prefixCls:t,children:r}=e,n=Mn(e,["prefixCls","children"]);const{getPrefixCls:o}=l.useContext(ie),a=o("form",t),i=l.useMemo(()=>({prefixCls:a,status:"error"}),[a]);return l.createElement(zt,Object.assign({},n),(c,s,m)=>l.createElement(ye.Provider,{value:i},r(c.map(x=>Object.assign(Object.assign({},x),{fieldKey:x.key})),s,{errors:m.errors,warnings:m.warnings})))};function _n(){const{form:e}=l.useContext(re);return e}const le=un;le.Item=rt;le.List=Rn;le.ErrorList=Qe;le.useForm=et;le.useFormInstance=_n;le.useWatch=Wt;le.Provider=De;le.create=()=>{};const Vn=e=>{const{componentCls:t,iconCls:r,antCls:n,zIndexPopup:o,colorText:a,colorWarning:i,marginXXS:c,marginXS:s,fontSize:m,fontWeightStrong:x,colorTextHeading:f}=e;return{[t]:{zIndex:o,[`&${n}-popover`]:{fontSize:m},[`${t}-message`]:{marginBottom:s,display:"flex",flexWrap:"nowrap",alignItems:"start",[`> ${t}-message-icon ${r}`]:{color:i,fontSize:m,lineHeight:1,marginInlineEnd:s},[`${t}-title`]:{fontWeight:x,color:f,"&:only-child":{fontWeight:"normal"}},[`${t}-description`]:{marginTop:c,color:a}},[`${t}-buttons`]:{textAlign:"end",whiteSpace:"nowrap",button:{marginInlineStart:s}}}}},Tn=e=>{const{zIndexPopupBase:t}=e;return{zIndexPopup:t+60}},ot=We("Popconfirm",e=>Vn(e),Tn,{resetStyle:!1});var Ln=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(r[n[o]]=e[n[o]]);return r};const lt=e=>{const{prefixCls:t,okButtonProps:r,cancelButtonProps:n,title:o,description:a,cancelText:i,okText:c,okType:s="primary",icon:m=l.createElement(ve,null),showCancel:x=!0,close:f,onConfirm:$,onCancel:N,onPopupClick:S}=e,{getPrefixCls:w}=l.useContext(ie),[b]=Ae("Popconfirm",ke.Popconfirm),y=Pe(o),F=Pe(a);return l.createElement("div",{className:`${t}-inner-content`,onClick:S},l.createElement("div",{className:`${t}-message`},m&&l.createElement("span",{className:`${t}-message-icon`},m),l.createElement("div",{className:`${t}-message-text`},y&&l.createElement("div",{className:`${t}-title`},y),F&&l.createElement("div",{className:`${t}-description`},F))),l.createElement("div",{className:`${t}-buttons`},x&&l.createElement(Ht,Object.assign({onClick:N,size:"small"},n),i||(b==null?void 0:b.cancelText)),l.createElement(Bt,{buttonProps:Object.assign(Object.assign({size:"small"},Dt(s)),r),actionFn:$,close:f,prefixCls:w("btn"),quitOnNullishReturnValue:!0,emitEvent:!0},c||(b==null?void 0:b.okText))))},zn=e=>{const{prefixCls:t,placement:r,className:n,style:o}=e,a=Ln(e,["prefixCls","placement","className","style"]),{getPrefixCls:i}=l.useContext(ie),c=i("popconfirm",t),[s]=ot(c);return s(l.createElement(Xt,{placement:r,className:Y(c,n),style:o,content:l.createElement(lt,Object.assign({prefixCls:c},a))}))};var Wn=function(e,t){var r={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(r[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var o=0,n=Object.getOwnPropertySymbols(e);o<n.length;o++)t.indexOf(n[o])<0&&Object.prototype.propertyIsEnumerable.call(e,n[o])&&(r[n[o]]=e[n[o]]);return r};const Hn=l.forwardRef((e,t)=>{var r,n;const{prefixCls:o,placement:a="top",trigger:i="click",okType:c="primary",icon:s=l.createElement(ve,null),children:m,overlayClassName:x,onOpenChange:f,onVisibleChange:$}=e,N=Wn(e,["prefixCls","placement","trigger","okType","icon","children","overlayClassName","onOpenChange","onVisibleChange"]),{getPrefixCls:S}=l.useContext(ie),[w,b]=qt(!1,{value:(r=e.open)!==null&&r!==void 0?r:e.visible,defaultValue:(n=e.defaultOpen)!==null&&n!==void 0?n:e.defaultVisible}),y=(C,O)=>{b(C,!0),$==null||$(C),f==null||f(C,O)},F=C=>{y(!1,C)},u=C=>{var O;return(O=e.onConfirm)===null||O===void 0?void 0:O.call(void 0,C)},g=C=>{var O;y(!1,C),(O=e.onCancel)===null||O===void 0||O.call(void 0,C)},d=(C,O)=>{const{disabled:_=!1}=e;_||y(C,O)},v=S("popconfirm",o),R=Y(v,x),[P]=ot(v);return P(l.createElement(Gt,Object.assign({},Xe(N,["title"]),{trigger:i,placement:a,onOpenChange:d,open:w,ref:t,overlayClassName:R,content:l.createElement(lt,Object.assign({okType:c,icon:s},e,{prefixCls:v,close:F,onConfirm:u,onCancel:g})),"data-popover-inject":!0}),m))}),Bn=Hn;Bn._InternalPanelDoNotUseOrYouWillBeFired=zn;export{le as F,Bn as P};
