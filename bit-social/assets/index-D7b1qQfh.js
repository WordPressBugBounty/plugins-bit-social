import{i as re,m as le,be as se,k as de,aR as Q,l as x,bf as ce,bg as pe,bh as ue,r as a,n as ne,aN as me,aZ as ge,aD as be,b as T,e as ve,a$ as $e,bi as fe,bj as we,o as Ce,bk as ye,bb as he,bl as Se,bm as xe,S as Pe,B as k}from"./main-spicy-waves-pick.js";import{R as Oe}from"./LeftOutlined-D7IKIZ4y.js";import{D as Ie}from"./Dropdown-DWF1R22X.js";import{M as Ee,O as ze}from"./index-C2wloJb5.js";import{i as ee}from"./move-BPAFbN3D.js";import{s as Ne,a as Re,b as je,c as De,i as oe,R as Te}from"./EllipsisOutlined-BaGkuCVJ.js";const Be=e=>typeof e!="object"&&typeof e!="function"||e===null,Me=e=>{const{componentCls:t,menuCls:o,colorError:i,colorTextLightSolid:l}=e,s=`${o}-item`;return{[`${t}, ${t}-menu-submenu`]:{[`${o} ${s}`]:{[`&${s}-danger:not(${s}-disabled)`]:{color:i,"&:hover":{color:l,backgroundColor:i}}}}}},Ae=e=>{const{componentCls:t,menuCls:o,zIndexPopup:i,dropdownArrowDistance:l,sizePopupArrow:s,antCls:n,iconCls:d,motionDurationMid:g,paddingBlock:w,fontSize:C,dropdownEdgeChildPadding:P,colorTextDisabled:u,fontSizeIcon:b,controlPaddingHorizontal:c,colorBgElevated:v}=e;return[{[t]:{position:"absolute",top:-9999,left:{_skip_check_:!0,value:-9999},zIndex:i,display:"block","&::before":{position:"absolute",insetBlock:e.calc(s).div(2).sub(l).equal(),zIndex:-9999,opacity:1e-4,content:'""'},"&-menu-vertical":{maxHeight:"100vh",overflowY:"auto"},[`&-trigger${n}-btn`]:{[`& > ${d}-down, & > ${n}-btn-icon > ${d}-down`]:{fontSize:b}},[`${t}-wrap`]:{position:"relative",[`${n}-btn > ${d}-down`]:{fontSize:b},[`${d}-down::before`]:{transition:`transform ${g}`}},[`${t}-wrap-open`]:{[`${d}-down::before`]:{transform:"rotate(180deg)"}},"\n        &-hidden,\n        &-menu-hidden,\n        &-menu-submenu-hidden\n      ":{display:"none"},[`&${n}-slide-down-enter${n}-slide-down-enter-active${t}-placement-bottomLeft,
          &${n}-slide-down-appear${n}-slide-down-appear-active${t}-placement-bottomLeft,
          &${n}-slide-down-enter${n}-slide-down-enter-active${t}-placement-bottom,
          &${n}-slide-down-appear${n}-slide-down-appear-active${t}-placement-bottom,
          &${n}-slide-down-enter${n}-slide-down-enter-active${t}-placement-bottomRight,
          &${n}-slide-down-appear${n}-slide-down-appear-active${t}-placement-bottomRight`]:{animationName:Ne},[`&${n}-slide-up-enter${n}-slide-up-enter-active${t}-placement-topLeft,
          &${n}-slide-up-appear${n}-slide-up-appear-active${t}-placement-topLeft,
          &${n}-slide-up-enter${n}-slide-up-enter-active${t}-placement-top,
          &${n}-slide-up-appear${n}-slide-up-appear-active${t}-placement-top,
          &${n}-slide-up-enter${n}-slide-up-enter-active${t}-placement-topRight,
          &${n}-slide-up-appear${n}-slide-up-appear-active${t}-placement-topRight`]:{animationName:Re},[`&${n}-slide-down-leave${n}-slide-down-leave-active${t}-placement-bottomLeft,
          &${n}-slide-down-leave${n}-slide-down-leave-active${t}-placement-bottom,
          &${n}-slide-down-leave${n}-slide-down-leave-active${t}-placement-bottomRight`]:{animationName:je},[`&${n}-slide-up-leave${n}-slide-up-leave-active${t}-placement-topLeft,
          &${n}-slide-up-leave${n}-slide-up-leave-active${t}-placement-top,
          &${n}-slide-up-leave${n}-slide-up-leave-active${t}-placement-topRight`]:{animationName:De}}},se(e,v,{arrowPlacement:{top:!0,bottom:!0}}),{[`${t} ${o}`]:{position:"relative",margin:0},[`${o}-submenu-popup`]:{position:"absolute",zIndex:i,background:"transparent",boxShadow:"none",transformOrigin:"0 0","ul, li":{listStyle:"none",margin:0}},[`${t}, ${t}-menu-submenu`]:Object.assign(Object.assign({},de(e)),{[o]:Object.assign(Object.assign({padding:P,listStyleType:"none",backgroundColor:v,backgroundClip:"padding-box",borderRadius:e.borderRadiusLG,outline:"none",boxShadow:e.boxShadowSecondary},Q(e)),{"&:empty":{padding:0,boxShadow:"none"},[`${o}-item-group-title`]:{padding:`${x(w)} ${x(c)}`,color:e.colorTextDescription,transition:`all ${g}`},[`${o}-item`]:{position:"relative",display:"flex",alignItems:"center"},[`${o}-item-icon`]:{minWidth:C,marginInlineEnd:e.marginXS,fontSize:e.fontSizeSM},[`${o}-title-content`]:{flex:"auto","&-with-extra":{display:"inline-flex",alignItems:"center",width:"100%"},"> a":{color:"inherit",transition:`all ${g}`,"&:hover":{color:"inherit"},"&::after":{position:"absolute",inset:0,content:'""'}},[`${o}-item-extra`]:{paddingInlineStart:e.padding,marginInlineStart:"auto",fontSize:e.fontSizeSM,color:e.colorTextDescription}},[`${o}-item, ${o}-submenu-title`]:Object.assign(Object.assign({display:"flex",margin:0,padding:`${x(w)} ${x(c)}`,color:e.colorText,fontWeight:"normal",fontSize:C,lineHeight:e.lineHeight,cursor:"pointer",transition:`all ${g}`,borderRadius:e.borderRadiusSM,"&:hover, &-active":{backgroundColor:e.controlItemBgHover}},Q(e)),{"&-selected":{color:e.colorPrimary,backgroundColor:e.controlItemBgActive,"&:hover, &-active":{backgroundColor:e.controlItemBgActiveHover}},"&-disabled":{color:u,cursor:"not-allowed","&:hover":{color:u,backgroundColor:v,cursor:"not-allowed"},a:{pointerEvents:"none"}},"&-divider":{height:1,margin:`${x(e.marginXXS)} 0`,overflow:"hidden",lineHeight:0,backgroundColor:e.colorSplit},[`${t}-menu-submenu-expand-icon`]:{position:"absolute",insetInlineEnd:e.paddingXS,[`${t}-menu-submenu-arrow-icon`]:{marginInlineEnd:"0 !important",color:e.colorTextDescription,fontSize:b,fontStyle:"normal"}}}),[`${o}-item-group-list`]:{margin:`0 ${x(e.marginXS)}`,padding:0,listStyle:"none"},[`${o}-submenu-title`]:{paddingInlineEnd:e.calc(c).add(e.fontSizeSM).equal()},[`${o}-submenu-vertical`]:{position:"relative"},[`${o}-submenu${o}-submenu-disabled ${t}-menu-submenu-title`]:{[`&, ${t}-menu-submenu-arrow-icon`]:{color:u,backgroundColor:v,cursor:"not-allowed"}},[`${o}-submenu-selected ${t}-menu-submenu-title`]:{color:e.colorPrimary}})})},[oe(e,"slide-up"),oe(e,"slide-down"),ee(e,"move-up"),ee(e,"move-down"),ce(e,"zoom-big")]]},Le=e=>Object.assign(Object.assign({zIndexPopup:e.zIndexPopupBase+50,paddingBlock:(e.controlHeight-e.fontSize*e.lineHeight)/2},pe({contentRadius:e.borderRadiusLG,limitVerticalRadius:!0})),ue(e)),_e=re("Dropdown",e=>{const{marginXXS:t,sizePopupArrow:o,paddingXXS:i,componentCls:l}=e,s=le(e,{menuCls:`${l}-menu`,dropdownArrowDistance:e.calc(o).div(2).add(t).equal(),dropdownEdgeChildPadding:i});return[Ae(s),Me(s)]},Le,{resetStyle:!1}),B=e=>{var t;const{menu:o,arrow:i,prefixCls:l,children:s,trigger:n,disabled:d,dropdownRender:g,getPopupContainer:w,overlayClassName:C,rootClassName:P,overlayStyle:u,open:b,onOpenChange:c,visible:v,onVisibleChange:O,mouseEnterDelay:M=.15,mouseLeaveDelay:A=.1,autoAdjustOverflow:L=!0,placement:m="",overlay:I,transitionName:E}=e,{getPopupContainer:_,getPrefixCls:z,direction:y,dropdown:$}=a.useContext(ne);he();const H=a.useMemo(()=>{const r=z();return E!==void 0?E:m.includes("top")?`${r}-slide-down`:`${r}-slide-up`},[z,m,E]),X=a.useMemo(()=>m?m.includes("Center")?m.slice(0,m.indexOf("Center")):m:y==="rtl"?"bottomRight":"bottomLeft",[m,y]),p=z("dropdown",l),N=me(p),[V,W,j]=_e(p,N),[,h]=ge(),R=a.Children.only(Be(s)?a.createElement("span",null,s):s),f=be(R,{className:T(`${p}-trigger`,{[`${p}-rtl`]:y==="rtl"},R.props.className),disabled:(t=R.props.disabled)!==null&&t!==void 0?t:d}),S=d?[]:n,U=!!(S!=null&&S.includes("contextMenu")),[F,D]=ve(!1,{value:b??v}),Z=$e(r=>{c==null||c(r,{source:"trigger"}),O==null||O(r),D(r)}),q=T(C,P,W,j,N,$==null?void 0:$.className,{[`${p}-rtl`]:y==="rtl"}),Y=fe({arrowPointAtCenter:typeof i=="object"&&i.pointAtCenter,autoAdjustOverflow:L,offset:h.marginXXS,arrowWidth:i?h.sizePopupArrow:0,borderRadius:h.borderRadius}),G=a.useCallback(()=>{o!=null&&o.selectable&&(o!=null&&o.multiple)||(c==null||c(!1,{source:"menu"}),D(!1))},[o==null?void 0:o.selectable,o==null?void 0:o.multiple]),ie=()=>{let r;return o!=null&&o.items?r=a.createElement(Ee,Object.assign({},o)):typeof I=="function"?r=I():r=I,g&&(r=g(r)),r=a.Children.only(typeof r=="string"?a.createElement("span",null,r):r),a.createElement(ze,{prefixCls:`${p}-menu`,rootClassName:T(j,N),expandIcon:a.createElement("span",{className:`${p}-menu-submenu-arrow`},a.createElement(Oe,{className:`${p}-menu-submenu-arrow-icon`})),mode:"vertical",selectable:!1,onClick:G,validator:Fe=>{}},r)},[K,ae]=we("Dropdown",u==null?void 0:u.zIndex);let J=a.createElement(Ie,Object.assign({alignPoint:U},Ce(e,["rootClassName"]),{mouseEnterDelay:M,mouseLeaveDelay:A,visible:F,builtinPlacements:Y,arrow:!!i,overlayClassName:q,prefixCls:p,getPopupContainer:w||_,transitionName:H,trigger:S,overlay:ie,placement:X,onVisibleChange:Z,overlayStyle:Object.assign(Object.assign(Object.assign({},$==null?void 0:$.style),u),{zIndex:K})}),f);return K&&(J=a.createElement(ye.Provider,{value:ae},J)),V(J)};function He(e){return Object.assign(Object.assign({},e),{align:{overflow:{adjustX:!1,adjustY:!1}}})}const Xe=Se(B,"dropdown",e=>e,He),Ve=e=>a.createElement(Xe,Object.assign({},e),a.createElement("span",null));B._InternalPanelDoNotUseOrYouWillBeFired=Ve;var We=function(e,t){var o={};for(var i in e)Object.prototype.hasOwnProperty.call(e,i)&&t.indexOf(i)<0&&(o[i]=e[i]);if(e!=null&&typeof Object.getOwnPropertySymbols=="function")for(var l=0,i=Object.getOwnPropertySymbols(e);l<i.length;l++)t.indexOf(i[l])<0&&Object.prototype.propertyIsEnumerable.call(e,i[l])&&(o[i[l]]=e[i[l]]);return o};const te=e=>{const{getPopupContainer:t,getPrefixCls:o,direction:i}=a.useContext(ne),{prefixCls:l,type:s="default",danger:n,disabled:d,loading:g,onClick:w,htmlType:C,children:P,className:u,menu:b,arrow:c,autoFocus:v,overlay:O,trigger:M,align:A,open:L,onOpenChange:m,placement:I,getPopupContainer:E,href:_,icon:z=a.createElement(Te,null),title:y,buttonsRender:$=G=>G,mouseEnterDelay:H,mouseLeaveDelay:X,overlayClassName:p,overlayStyle:N,destroyPopupOnHide:V,dropdownRender:W}=e,j=We(e,["prefixCls","type","danger","disabled","loading","onClick","htmlType","children","className","menu","arrow","autoFocus","overlay","trigger","align","open","onOpenChange","placement","getPopupContainer","href","icon","title","buttonsRender","mouseEnterDelay","mouseLeaveDelay","overlayClassName","overlayStyle","destroyPopupOnHide","dropdownRender"]),h=o("dropdown",l),R=`${h}-button`,f={menu:b,arrow:c,autoFocus:v,align:A,disabled:d,trigger:d?[]:M,onOpenChange:m,getPopupContainer:E||t,mouseEnterDelay:H,mouseLeaveDelay:X,overlayClassName:p,overlayStyle:N,destroyPopupOnHide:V,dropdownRender:W},{compactSize:S,compactItemClassnames:U}=xe(h,i),F=T(R,U,u);"overlay"in e&&(f.overlay=O),"open"in e&&(f.open=L),"placement"in e?f.placement=I:f.placement=i==="rtl"?"bottomLeft":"bottomRight";const D=a.createElement(k,{type:s,danger:n,disabled:d,loading:g,onClick:w,htmlType:C,href:_,title:y},P),Z=a.createElement(k,{type:s,danger:n,icon:z}),[q,Y]=$([D,Z]);return a.createElement(Pe.Compact,Object.assign({className:F,size:S,block:!0},j),q,a.createElement(B,Object.assign({},f),Y))};te.__ANT_BUTTON=!0;const Ue=B;Ue.Button=te;export{Ue as D};
