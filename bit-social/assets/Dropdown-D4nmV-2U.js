import{r as C,aK as te,aQ as L,c as W,bJ as D,aw as n,b1 as _,d as ne,_ as ae,b as S,bq as re,f as oe,g as ie}from"./main-honest-apples-shout.js";var le=L.ESC,se=L.TAB;function fe(e){var u=e.visible,r=e.triggerRef,s=e.onVisibleChange,c=e.autoFocus,o=e.overlayRef,a=C.useRef(!1),f=function(){if(u){var t,i;(t=r.current)===null||t===void 0||(i=t.focus)===null||i===void 0||i.call(t),s==null||s(!1)}},h=function(){var t;return(t=o.current)!==null&&t!==void 0&&t.focus?(o.current.focus(),a.current=!0,!0):!1},p=function(t){switch(t.keyCode){case le:f();break;case se:{var i=!1;a.current||(i=h()),i?t.preventDefault():f();break}}};C.useEffect(function(){return u?(window.addEventListener("keydown",p),c&&te(h,3),function(){window.removeEventListener("keydown",p),a.current=!1}):function(){a.current=!1}},[u])}var ue=C.forwardRef(function(e,u){var r=e.overlay,s=e.arrow,c=e.prefixCls,o=C.useMemo(function(){var f;return typeof r=="function"?f=r():f=r,f},[r]),a=W(u,D(o));return n.createElement(n.Fragment,null,s&&n.createElement("div",{className:"".concat(c,"-arrow")}),n.cloneElement(o,{ref:_(o)?a:void 0}))}),v={adjustX:1,adjustY:1},d=[0,0],ce={topLeft:{points:["bl","tl"],overflow:v,offset:[0,-4],targetOffset:d},top:{points:["bc","tc"],overflow:v,offset:[0,-4],targetOffset:d},topRight:{points:["br","tr"],overflow:v,offset:[0,-4],targetOffset:d},bottomLeft:{points:["tl","bl"],overflow:v,offset:[0,4],targetOffset:d},bottom:{points:["tc","bc"],overflow:v,offset:[0,4],targetOffset:d},bottomRight:{points:["tr","br"],overflow:v,offset:[0,4],targetOffset:d}},ve=["arrow","prefixCls","transitionName","animation","align","placement","placements","getPopupContainer","showAction","hideAction","overlayClassName","overlayStyle","visible","trigger","autoFocus","overlay","children","onVisibleChange"];function de(e,u){var r,s=e.arrow,c=s===void 0?!1:s,o=e.prefixCls,a=o===void 0?"rc-dropdown":o,f=e.transitionName,h=e.animation,p=e.align,g=e.placement,t=g===void 0?"bottomLeft":g,i=e.placements,j=i===void 0?ce:i,K=e.getPopupContainer,$=e.showAction,B=e.hideAction,H=e.overlayClassName,q=e.overlayStyle,I=e.visible,N=e.trigger,A=N===void 0?["hover"]:N,J=e.autoFocus,M=e.overlay,y=e.children,w=e.onVisibleChange,Q=ne(e,ve),X=n.useState(),E=ae(X,2),Y=E[0],x=E[1],R="visible"in e?I:Y,P=n.useRef(null),V=n.useRef(null),k=n.useRef(null);n.useImperativeHandle(u,function(){return P.current});var T=function(l){x(l),w==null||w(l)};fe({visible:R,triggerRef:k,onVisibleChange:T,autoFocus:J,overlayRef:V});var z=function(l){var b=e.onOverlayClick;x(!1),b&&b(l)},F=function(){return n.createElement(ue,{ref:V,overlay:M,prefixCls:a,arrow:c})},G=function(){return typeof M=="function"?F:F()},U=function(){var l=e.minOverlayWidthMatchTrigger,b=e.alignPoint;return"minOverlayWidthMatchTrigger"in e?l:!b},Z=function(){var l=e.openClassName;return l!==void 0?l:"".concat(a,"-open")},ee=n.cloneElement(y,{className:S((r=y.props)===null||r===void 0?void 0:r.className,R&&Z()),ref:_(y)?W(k,D(y)):void 0}),O=B;return!O&&A.indexOf("contextMenu")!==-1&&(O=["click"]),n.createElement(re,oe({builtinPlacements:j},Q,{prefixCls:a,ref:P,popupClassName:S(H,ie({},"".concat(a,"-show-arrow"),c)),popupStyle:q,action:A,showAction:$,hideAction:O,popupPlacement:t,popupAlign:p,popupTransitionName:f,popupAnimation:h,popupVisible:R,stretch:U()?"minWidth":"",popup:G(),onPopupVisibleChange:T,onPopupClick:z,getPopupContainer:K}),ee)}const me=n.forwardRef(de);export{me as D};
