import{ah as t,cu as s}from"./main-spicy-waves-pick.js";const m=new t("antMoveDownIn",{"0%":{transform:"translate3d(0, 100%, 0)",transformOrigin:"0 0",opacity:0},"100%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1}}),f=new t("antMoveDownOut",{"0%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1},"100%":{transform:"translate3d(0, 100%, 0)",transformOrigin:"0 0",opacity:0}}),c=new t("antMoveLeftIn",{"0%":{transform:"translate3d(-100%, 0, 0)",transformOrigin:"0 0",opacity:0},"100%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1}}),y=new t("antMoveLeftOut",{"0%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1},"100%":{transform:"translate3d(-100%, 0, 0)",transformOrigin:"0 0",opacity:0}}),p=new t("antMoveRightIn",{"0%":{transform:"translate3d(100%, 0, 0)",transformOrigin:"0 0",opacity:0},"100%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1}}),O=new t("antMoveRightOut",{"0%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1},"100%":{transform:"translate3d(100%, 0, 0)",transformOrigin:"0 0",opacity:0}}),g=new t("antMoveUpIn",{"0%":{transform:"translate3d(0, -100%, 0)",transformOrigin:"0 0",opacity:0},"100%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1}}),v=new t("antMoveUpOut",{"0%":{transform:"translate3d(0, 0, 0)",transformOrigin:"0 0",opacity:1},"100%":{transform:"translate3d(0, -100%, 0)",transformOrigin:"0 0",opacity:0}}),u={"move-up":{inKeyframes:g,outKeyframes:v},"move-down":{inKeyframes:m,outKeyframes:f},"move-left":{inKeyframes:c,outKeyframes:y},"move-right":{inKeyframes:p,outKeyframes:O}},d=(n,a)=>{const{antCls:o}=n,r=`${o}-${a}`,{inKeyframes:i,outKeyframes:e}=u[a];return[s(r,i,e,n.motionDurationMid),{[`
        ${r}-enter,
        ${r}-appear
      `]:{opacity:0,animationTimingFunction:n.motionEaseOutCirc},[`${r}-leave`]:{animationTimingFunction:n.motionEaseInOutCirc}}]};export{d as i};
