import{aZ as F,aw as Q,r as u,i as T,m as Z,l as K,n as _,b as J}from"./main-honest-apples-shout.js";const b=["xxl","xl","lg","md","sm","xs"],Y=e=>({xs:`(max-width: ${e.screenXSMax}px)`,sm:`(min-width: ${e.screenSM}px)`,md:`(min-width: ${e.screenMD}px)`,lg:`(min-width: ${e.screenLG}px)`,xl:`(min-width: ${e.screenXL}px)`,xxl:`(min-width: ${e.screenXXL}px)`}),z=e=>{const t=e,i=[].concat(b).reverse();return i.forEach((n,s)=>{const o=n.toUpperCase(),r=`screen${o}Min`,a=`screen${o}`;if(!(t[r]<=t[a]))throw new Error(`${r}<=${a} fails : !(${t[r]}<=${t[a]})`);if(s<i.length-1){const l=`screen${o}Max`;if(!(t[a]<=t[l]))throw new Error(`${a}<=${l} fails : !(${t[a]}<=${t[l]})`);const $=`screen${i[s+1].toUpperCase()}Min`;if(!(t[l]<=t[$]))throw new Error(`${l}<=${$} fails : !(${t[l]}<=${t[$]})`)}}),e};function ee(){const[,e]=F(),t=Y(z(e));return Q.useMemo(()=>{const i=new Map;let n=-1,s={};return{matchHandlers:{},dispatch(o){return s=o,i.forEach(r=>r(s)),i.size>=1},subscribe(o){return i.size||this.register(),n+=1,i.set(n,o),o(s),n},unsubscribe(o){i.delete(o),i.size||this.unregister()},unregister(){Object.keys(t).forEach(o=>{const r=t[o],a=this.matchHandlers[r];a==null||a.mql.removeListener(a==null?void 0:a.listener)}),i.clear()},register(){Object.keys(t).forEach(o=>{const r=t[o],a=g=>{let{matches:$}=g;this.dispatch(Object.assign(Object.assign({},s),{[o]:$}))},l=window.matchMedia(r);l.addListener(a),this.matchHandlers[r]={mql:l,listener:a},a(l)})},responsiveMap:t}},[e])}const q=u.createContext({}),te=e=>{const{componentCls:t}=e;return{[t]:{display:"flex",flexFlow:"row wrap",minWidth:0,"&::before, &::after":{display:"flex"},"&-no-wrap":{flexWrap:"nowrap"},"&-start":{justifyContent:"flex-start"},"&-center":{justifyContent:"center"},"&-end":{justifyContent:"flex-end"},"&-space-between":{justifyContent:"space-between"},"&-space-around":{justifyContent:"space-around"},"&-space-evenly":{justifyContent:"space-evenly"},"&-top":{alignItems:"flex-start"},"&-middle":{alignItems:"center"},"&-bottom":{alignItems:"flex-end"}}}},ne=e=>{const{componentCls:t}=e;return{[t]:{position:"relative",maxWidth:"100%",minHeight:1}}},re=(e,t)=>{const{prefixCls:i,componentCls:n,gridColumns:s}=e,o={};for(let r=s;r>=0;r--)r===0?(o[`${n}${t}-${r}`]={display:"none"},o[`${n}-push-${r}`]={insetInlineStart:"auto"},o[`${n}-pull-${r}`]={insetInlineEnd:"auto"},o[`${n}${t}-push-${r}`]={insetInlineStart:"auto"},o[`${n}${t}-pull-${r}`]={insetInlineEnd:"auto"},o[`${n}${t}-offset-${r}`]={marginInlineStart:0},o[`${n}${t}-order-${r}`]={order:0}):(o[`${n}${t}-${r}`]=[{"--ant-display":"block",display:"block"},{display:"var(--ant-display)",flex:`0 0 ${r/s*100}%`,maxWidth:`${r/s*100}%`}],o[`${n}${t}-push-${r}`]={insetInlineStart:`${r/s*100}%`},o[`${n}${t}-pull-${r}`]={insetInlineEnd:`${r/s*100}%`},o[`${n}${t}-offset-${r}`]={marginInlineStart:`${r/s*100}%`},o[`${n}${t}-order-${r}`]={order:r});return o[`${n}${t}-flex`]={flex:`var(--${i}${t}-flex)`},o},H=(e,t)=>re(e,t),se=(e,t,i)=>({[`@media (min-width: ${K(t)})`]:Object.assign({},H(e,i))}),oe=()=>({}),ie=()=>({}),ae=T("Grid",te,oe),ce=T("Grid",e=>{const t=Z(e,{gridColumns:24}),i={"-sm":t.screenSMMin,"-md":t.screenMDMin,"-lg":t.screenLGMin,"-xl":t.screenXLMin,"-xxl":t.screenXXLMin};return[ne(t),H(t,""),H(t,"-xs"),Object.keys(i).map(n=>se(t,i[n],n)).reduce((n,s)=>Object.assign(Object.assign({},n),s),{})]},ie);var le=function(e,t){var i={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(i[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var s=0,n=Object.getOwnPropertySymbols(e);s<n.length;s++)t.indexOf(n[s])<0&&Object.prototype.propertyIsEnumerable.call(e,n[s])&&(i[n[s]]=e[n[s]]);return i};function W(e){return typeof e=="number"?`${e} ${e} auto`:/^\d+(\.\d+)?(px|em|rem|%)$/.test(e)?`0 0 ${e}`:e}const fe=["xs","sm","md","lg","xl","xxl"],de=u.forwardRef((e,t)=>{const{getPrefixCls:i,direction:n}=u.useContext(_),{gutter:s,wrap:o}=u.useContext(q),{prefixCls:r,span:a,order:l,offset:g,push:$,pull:w,className:G,children:I,flex:j,style:v}=e,O=le(e,["prefixCls","span","order","offset","push","pull","className","children","flex","style"]),f=i("col",r),[S,R,C]=ce(f),M={};let d={};fe.forEach(p=>{let c={};const y=e[p];typeof y=="number"?c.span=y:typeof y=="object"&&(c=y||{}),delete O[p],d=Object.assign(Object.assign({},d),{[`${f}-${p}-${c.span}`]:c.span!==void 0,[`${f}-${p}-order-${c.order}`]:c.order||c.order===0,[`${f}-${p}-offset-${c.offset}`]:c.offset||c.offset===0,[`${f}-${p}-push-${c.push}`]:c.push||c.push===0,[`${f}-${p}-pull-${c.pull}`]:c.pull||c.pull===0,[`${f}-rtl`]:n==="rtl"}),c.flex&&(d[`${f}-${p}-flex`]=!0,M[`--${f}-${p}-flex`]=W(c.flex))});const k=J(f,{[`${f}-${a}`]:a!==void 0,[`${f}-order-${l}`]:l,[`${f}-offset-${g}`]:g,[`${f}-push-${$}`]:$,[`${f}-pull-${w}`]:w},G,d,R,C),x={};if(s&&s[0]>0){const p=s[0]/2;x.paddingLeft=p,x.paddingRight=p}return j&&(x.flex=W(j),o===!1&&!x.minWidth&&(x.minWidth=0)),S(u.createElement("div",Object.assign({},O,{style:Object.assign(Object.assign(Object.assign({},x),v),M),className:k,ref:t}),I))});var ue=function(e,t){var i={};for(var n in e)Object.prototype.hasOwnProperty.call(e,n)&&t.indexOf(n)<0&&(i[n]=e[n]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var s=0,n=Object.getOwnPropertySymbols(e);s<n.length;s++)t.indexOf(n[s])<0&&Object.prototype.propertyIsEnumerable.call(e,n[s])&&(i[n[s]]=e[n[s]]);return i};function B(e,t){const[i,n]=u.useState(typeof e=="string"?e:""),s=()=>{if(typeof e=="string"&&n(e),typeof e=="object")for(let o=0;o<b.length;o++){const r=b[o];if(!t[r])continue;const a=e[r];if(a!==void 0){n(a);return}}};return u.useEffect(()=>{s()},[JSON.stringify(e),t]),i}const $e=u.forwardRef((e,t)=>{const{prefixCls:i,justify:n,align:s,className:o,style:r,children:a,gutter:l=0,wrap:g}=e,$=ue(e,["prefixCls","justify","align","className","style","children","gutter","wrap"]),{getPrefixCls:w,direction:G}=u.useContext(_),[I,j]=u.useState({xs:!0,sm:!0,md:!0,lg:!0,xl:!0,xxl:!0}),[v,O]=u.useState({xs:!1,sm:!1,md:!1,lg:!1,xl:!1,xxl:!1}),f=B(s,v),S=B(n,v),R=u.useRef(l),C=ee();u.useEffect(()=>{const h=C.subscribe(N=>{O(N);const m=R.current||0;(!Array.isArray(m)&&typeof m=="object"||Array.isArray(m)&&(typeof m[0]=="object"||typeof m[1]=="object"))&&j(N)});return()=>C.unsubscribe(h)},[]);const M=()=>{const h=[void 0,void 0];return(Array.isArray(l)?l:[l,void 0]).forEach((m,V)=>{if(typeof m=="object")for(let P=0;P<b.length;P++){const X=b[P];if(I[X]&&m[X]!==void 0){h[V]=m[X];break}}else h[V]=m}),h},d=w("row",i),[k,x,p]=ae(d),c=M(),y=J(d,{[`${d}-no-wrap`]:g===!1,[`${d}-${S}`]:S,[`${d}-${f}`]:f,[`${d}-rtl`]:G==="rtl"},o,x,p),E={},L=c[0]!=null&&c[0]>0?c[0]/-2:void 0;L&&(E.marginLeft=L,E.marginRight=L);const[U,A]=c;E.rowGap=A;const D=u.useMemo(()=>({gutter:[U,A],wrap:g}),[U,A,g]);return k(u.createElement(q.Provider,{value:D},u.createElement("div",Object.assign({},$,{className:y,style:Object.assign(Object.assign({},E),r),ref:t}),a)))});export{de as C,$e as R,b as r,ee as u};
