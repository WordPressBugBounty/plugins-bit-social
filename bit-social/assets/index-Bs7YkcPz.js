import{m as n,i as l,k as e,aL as t,l as a}from"./main-spicy-waves-pick.js";const c=r=>{const{checkboxCls:o}=r,i=`${o}-wrapper`;return[{[`${o}-group`]:Object.assign(Object.assign({},e(r)),{display:"inline-flex",flexWrap:"wrap",columnGap:r.marginXS,[`> ${r.antCls}-row`]:{flex:1}}),[i]:Object.assign(Object.assign({},e(r)),{display:"inline-flex",alignItems:"baseline",cursor:"pointer","&:after":{display:"inline-block",width:0,overflow:"hidden",content:"'\\a0'"},[`& + ${i}`]:{marginInlineStart:0},[`&${i}-in-form-item`]:{'input[type="checkbox"]':{width:14,height:14}}}),[o]:Object.assign(Object.assign({},e(r)),{position:"relative",whiteSpace:"nowrap",lineHeight:1,cursor:"pointer",borderRadius:r.borderRadiusSM,alignSelf:"center",[`${o}-input`]:{position:"absolute",inset:0,zIndex:1,cursor:"pointer",opacity:0,margin:0,[`&:focus-visible + ${o}-inner`]:Object.assign({},t(r))},[`${o}-inner`]:{boxSizing:"border-box",display:"block",width:r.checkboxSize,height:r.checkboxSize,direction:"ltr",backgroundColor:r.colorBgContainer,border:`${a(r.lineWidth)} ${r.lineType} ${r.colorBorder}`,borderRadius:r.borderRadiusSM,borderCollapse:"separate",transition:`all ${r.motionDurationSlow}`,"&:after":{boxSizing:"border-box",position:"absolute",top:"50%",insetInlineStart:"25%",display:"table",width:r.calc(r.checkboxSize).div(14).mul(5).equal(),height:r.calc(r.checkboxSize).div(14).mul(8).equal(),border:`${a(r.lineWidthBold)} solid ${r.colorWhite}`,borderTop:0,borderInlineStart:0,transform:"rotate(45deg) scale(0) translate(-50%,-50%)",opacity:0,content:'""',transition:`all ${r.motionDurationFast} ${r.motionEaseInBack}, opacity ${r.motionDurationFast}`}},"& + span":{paddingInlineStart:r.paddingXS,paddingInlineEnd:r.paddingXS}})},{[`
        ${i}:not(${i}-disabled),
        ${o}:not(${o}-disabled)
      `]:{[`&:hover ${o}-inner`]:{borderColor:r.colorPrimary}},[`${i}:not(${i}-disabled)`]:{[`&:hover ${o}-checked:not(${o}-disabled) ${o}-inner`]:{backgroundColor:r.colorPrimaryHover,borderColor:"transparent"},[`&:hover ${o}-checked:not(${o}-disabled):after`]:{borderColor:r.colorPrimaryHover}}},{[`${o}-checked`]:{[`${o}-inner`]:{backgroundColor:r.colorPrimary,borderColor:r.colorPrimary,"&:after":{opacity:1,transform:"rotate(45deg) scale(1) translate(-50%,-50%)",transition:`all ${r.motionDurationMid} ${r.motionEaseOutBack} ${r.motionDurationFast}`}}},[`
        ${i}-checked:not(${i}-disabled),
        ${o}-checked:not(${o}-disabled)
      `]:{[`&:hover ${o}-inner`]:{backgroundColor:r.colorPrimaryHover,borderColor:"transparent"}}},{[o]:{"&-indeterminate":{[`${o}-inner`]:{backgroundColor:`${r.colorBgContainer} !important`,borderColor:`${r.colorBorder} !important`,"&:after":{top:"50%",insetInlineStart:"50%",width:r.calc(r.fontSizeLG).div(2).equal(),height:r.calc(r.fontSizeLG).div(2).equal(),backgroundColor:r.colorPrimary,border:0,transform:"translate(-50%, -50%) scale(1)",opacity:1,content:'""'}},[`&:hover ${o}-inner`]:{backgroundColor:`${r.colorBgContainer} !important`,borderColor:`${r.colorPrimary} !important`}}}},{[`${i}-disabled`]:{cursor:"not-allowed"},[`${o}-disabled`]:{[`&, ${o}-input`]:{cursor:"not-allowed",pointerEvents:"none"},[`${o}-inner`]:{background:r.colorBgContainerDisabled,borderColor:r.colorBorder,"&:after":{borderColor:r.colorTextDisabled}},"&:after":{display:"none"},"& + span":{color:r.colorTextDisabled},[`&${o}-indeterminate ${o}-inner::after`]:{background:r.colorTextDisabled}}}]};function d(r,o){const i=n(o,{checkboxCls:`.${r}`,checkboxSize:o.controlInteractiveSize});return[c(i)]}const b=l("Checkbox",(r,o)=>{let{prefixCls:i}=o;return[d(i,r)]});export{d as g,b as u};
